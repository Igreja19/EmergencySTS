<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use backend\modules\api\controllers\BaseActiveController;

use common\models\Consulta;
use common\models\UserProfile;
use common\models\Triagem;

class ConsultaController extends BaseActiveController
{
    public $modelClass = 'common\models\Consulta';
    public $enableCsrfValidation = false;

    // NOTA: A função behaviors() foi removida porque já é herdada do BaseActiveController

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }


    //  GET: LISTAR TODAS AS CONSULTAS (/api/consulta)
    public function actionIndex()
    {
        // Se for PACIENTE, bloqueia imediatamente.
        // O paciente não pode ver a lista global de consultas do hospital.
        if (Yii::$app->user->can('paciente')) {
            throw new ForbiddenHttpException("Acesso negado. Utilize a rota de histórico pessoal.");
        }

        // Daqui para baixo, apenas Médicos, Enfermeiros e Admin passam.
        $consultas = Consulta::find()
            ->orderBy(['data_consulta' => SORT_DESC])
            ->all();

        $data = [];
        foreach ($consultas as $consulta) {
            $data[] = [
                'id'             => $consulta->id,
                'userprofile_id' => $consulta->userprofile_id,
                'data_consulta'  => $consulta->data_consulta,
                'estado'         => $consulta->estado,
                'observacoes'    => $consulta->observacoes,
                'triagem'        => $consulta->triagem ? [
                    'id'         => $consulta->triagem->id,
                    'prioridade' => $consulta->triagem->prioridadeatribuida ?? 'N/A',
                    'queixa'     => $consulta->triagem->queixaprincipal,
                ] : null,
            ];
        }

        return [
            'status' => 'success',
            'total'  => count($data),
            'data'   => $data,
        ];
    }

    //  GET: HISTÓRICO DO PACIENTE (/api/userprofiles/{id}/consultas)
    public function actionHistorico($id)
    {
        // Verificação de segurança para Pacientes
        if (Yii::$app->user->can('paciente')) {
             $meuProfile = UserProfile::findOne(['user_id' => Yii::$app->user->id]);
             // Só deixa ver se o ID solicitado for o do próprio paciente
             if (!$meuProfile || $meuProfile->id != $id) {
                  throw new ForbiddenHttpException("Apenas pode ver o seu histórico.");
             }
        }

        // Se for Médico/Enfermeiro, passa direto.
        $profile = UserProfile::findOne($id);
        if (!$profile) {
             throw new NotFoundHttpException("Perfil não encontrado.");
        }

        $consultas = Consulta::find()
            ->where(['userprofile_id' => $id])
            ->orderBy(['data_consulta' => SORT_DESC])
            ->all();

        $data = [];
        foreach ($consultas as $consulta) {
            $data[] = [
                'id'            => $consulta->id,
                'data'          => $consulta->data_consulta,
                'estado'        => $consulta->estado,
                'observacoes'   => $consulta->observacoes,
                'relatorio_pdf' => $consulta->relatorio_pdf,
                'triagem' => $consulta->triagem ? [
                    'queixa'     => $consulta->triagem->queixaprincipal,
                    'prioridade' => $consulta->triagem->prioridadeatribuida ?? 'N/A',
                ] : null,
            ];
        }

        return [
            'status' => 'success',
            'total'  => count($data),
            'data'   => $data,
        ];
    }

    //  POST: INICIAR CONSULTA (/api/consulta)
    public function actionCreate()
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem iniciar consultas.");
        }

        $data = Yii::$app->request->getBodyParams();

        if (empty($data['triagem_id'])) {
            throw new BadRequestHttpException("É necessário indicar o 'triagem_id'.");
        }

        $triagem = Triagem::findOne($data['triagem_id']);
        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        // --- CORREÇÃO: Obter o perfil do Médico Logado ---
        $medicoProfile = UserProfile::findOne(['user_id' => Yii::$app->user->id]);
        if (!$medicoProfile) {
            throw new BadRequestHttpException("Erro: Não foi encontrado um perfil de médico associado a esta conta.");
        }

        $consulta = new Consulta();
        $consulta->triagem_id = $triagem->id;
        $consulta->userprofile_id = $triagem->userprofile_id;
        
        // Agora preenchemos o campo obrigatório com o ID do médico
        $consulta->medicouserprofile_id = $medicoProfile->id; 
        
        $consulta->data_consulta = date('Y-m-d H:i:s');
        $consulta->estado = 'Em curso';

        // Preenche o nome do médico também (opcional)
        if ($consulta->hasAttribute('medico_nome')) {
            $consulta->medico_nome = $medicoProfile->nome;
        }

        if (isset($data['observacoes'])) {
            $consulta->observacoes = $data['observacoes'];
        }

        if ($consulta->save()) {

            // Atualizar pulseira → Em atendimento
            if ($triagem->pulseira) {
                $triagem->pulseira->status = 'Em atendimento';
                $triagem->pulseira->save();

                // MQTT Pulseira
                $this->safeMqttPublish("pulseira/atualizada/{$triagem->pulseira->id}", [
                    'evento'      => 'pulseira_atualizada',
                    'pulseira_id' => $triagem->pulseira->id,
                    'status'      => 'Em atendimento',
                    'hora'        => date('Y-m-d H:i:s'),
                ]);
            }

            // MQTT Consulta Criada
            $this->safeMqttPublish("consulta/criada/{$consulta->id}", [
                'evento'        => 'consulta_criada',
                'consulta_id'   => $consulta->id,
                'userprofile_id'=> $consulta->userprofile_id,
                'triagem_id'    => $consulta->triagem_id,
                'medico_id'     => $medicoProfile->id,
                'estado'        => $consulta->estado,
                'hora'          => date('Y-m-d H:i:s'),
            ]);

            Yii::$app->response->statusCode = 201; // Created
            return [
                'status'  => 'success',
                'message' => 'Consulta iniciada.',
                'data'    => $consulta,
            ];
        }

        Yii::$app->response->statusCode = 422;
        return ['status' => 'error', 'errors' => $consulta->getErrors()];
    }

    //  PUT: ATUALIZAR / ENCERRAR (/api/consulta/{id})
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem atualizar consultas.");
        }

        $consulta = Consulta::findOne($id);
        if (!$consulta) {
            throw new NotFoundHttpException("Consulta não encontrada.");
        }

        $data = Yii::$app->request->getBodyParams();

        if (isset($data['observacoes'])) {
            $consulta->observacoes = $data['observacoes'];
        }
        if (isset($data['estado'])) {
            $consulta->estado = $data['estado'];
        }

        if ($consulta->save()) {

            // MQTT Genérico (Atualizada)
            $this->safeMqttPublish("consulta/atualizada/{$consulta->id}", [
                'evento'      => 'consulta_atualizada',
                'consulta_id' => $consulta->id,
                'estado'      => $consulta->estado,
                'hora'        => date('Y-m-d H:i:s'),
            ]);

            // Lógica específica se Encerrada
            if ($consulta->estado === 'Encerrada') {
                $triagem = Triagem::findOne($consulta->triagem_id);

                if ($triagem && $triagem->pulseira) {
                    $triagem->pulseira->status = 'Atendido';
                    $triagem->pulseira->save();

                    // MQTT Pulseira (Atendido)
                    $this->safeMqttPublish("pulseira/atualizada/{$triagem->pulseira->id}", [
                        'evento'      => 'pulseira_atualizada',
                        'pulseira_id' => $triagem->pulseira->id,
                        'status'      => 'Atendido',
                        'hora'        => date('Y-m-d H:i:s'),
                    ]);

                    // MQTT Consulta (Encerrada)
                    $this->safeMqttPublish("consulta/encerrada/{$consulta->id}", [
                        'evento'      => 'consulta_encerrada',
                        'consulta_id' => $consulta->id,
                        'hora'        => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            return [
                'status'  => 'success',
                'message' => 'Consulta atualizada.',
                'data'    => $consulta,
            ];
        }

        Yii::$app->response->statusCode = 422;
        return ['status' => 'error', 'errors' => $consulta->getErrors()];
    }

    /**
     * Função auxiliar interna para evitar repetição de código try-catch do MQTT
     */
    protected function safeMqttPublish($topic, $payload)
    {
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish($topic, json_encode($payload));
            } catch (\Exception $e) {
                Yii::error("Erro MQTT ({$topic}): " . $e->getMessage());
            }
        }
    }
}