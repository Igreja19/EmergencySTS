<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\data\ActiveDataProvider;
use backend\modules\api\controllers\BaseActiveController;
use common\models\Triagem;
use common\models\UserProfile;
use common\models\Pulseira;

class TriagemController extends BaseActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user;

        // Performance: Carregar logo as relações
        $query = Triagem::find()
            ->with(['userprofile', 'pulseira'])
            ->orderBy(['datatriagem' => SORT_DESC]);

        // Se for Paciente, aplica o filtro (vê a sua própria lista)
        // Se for Staff, vê tudo.
        if ($user->can('paciente')) {
            $query->joinWith(['userprofile' => function ($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

        // Filtro por pulseira
        if ($p = Yii::$app->request->get('pulseira_id')) {
            $query->andWhere(['pulseira_id' => $p]);
        }

        return new ActiveDataProvider([
            "query" => $query,
            "pagination" => false 
        ]);
    }

    public function actionView($id)
    {
        $t = Triagem::find()
            ->where(['id' => $id])
            ->with(['userprofile', 'pulseira'])
            ->one();

        if (!$t) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        // Paciente só vê a SUA triagem específica
        if (Yii::$app->user->can('paciente')) {
            $donoId = $t->userprofile->user_id ?? null;
            if ($donoId != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta triagem.");
            }
        }

        return $t;
    }

    public function actionCreate()
    {
        // Apenas Staff cria triagens
        if (Yii::$app->user->can('paciente')) {
            throw new ForbiddenHttpException("Apenas profissionais podem registar triagens.");
        }

        $data = Yii::$app->request->post();

        if (empty($data['userprofile_id'])) {
            throw new BadRequestHttpException("ID do utente é obrigatório.");
        }

        $tx = Yii::$app->db->beginTransaction();

        try {
            $t = new Triagem();
            $t->load($data, '');
            $t->userprofile_id = $data['userprofile_id'];
            $t->datatriagem = date("Y-m-d H:i:s");

            if (!$t->save()) {
                throw new \Exception("Erro ao guardar triagem: " . json_encode($t->errors));
            }

            // Criação automática de Pulseira
            $p = new Pulseira([
                "userprofile_id" => $t->userprofile_id,
                "codigo"         => "P-" . strtoupper(substr(uniqid(), -5)),
                "prioridade"     => "Pendente",
                "status"         => "Em espera",
                "tempoentrada"   => date('Y-m-d H:i:s')
            ]);

            if (!$p->save()) {
                throw new \Exception("Erro ao gerar pulseira.");
            }

            // Atualiza triagem com ID da pulseira
            $t->pulseira_id = $p->id;
            $t->save(false);

            $tx->commit();

            $this->safeMqttPublish("triagem/criada/{$t->id}", [
                'titulo'          => 'Nova Triagem',
                'mensagem'        => "Nova pulseira criada: {$p->codigo}. Em espera.", // <--- Texto importante
                "evento"          => "triagem_criada",
                "triagem_id"      => $t->id,
                "pulseira_codigo" => $p->codigo,
                "hora"            => date("Y-m-d H:i:s")
            ]);

            return ["status" => "success", "triagem" => $t, "pulseira" => $p];

        } catch (\Exception $e) {
            $tx->rollBack();
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function actionUpdate($id)
    {
        // Verifica permissões
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico')) {
            throw new ForbiddenHttpException("Sem permissão para editar triagens.");
        }

        // Encontra a triagem
        $t = Triagem::findOne($id);
        if (!$t) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        // Carrega os dados novos (ex: a cor/classificação)
        $t->load(Yii::$app->request->post(), '');

        if ($t->save()) {

            // 1. Tentar obter o nome do paciente (se a relação existir)
            $nomePaciente = "Utente";
            if ($t->userprofile) {
                $nomePaciente = $t->userprofile->nome;
            }

            $cor = $t->classificacao ?? "Prioridade definida";

            // 3. Enviar para o MQTT
            $this->safeMqttPublish("triagem/atualizada/{$t->id}", [
                'titulo'     => 'Triagem Concluída',
                // AQUI está o texto que vai aparecer no telemóvel:
                'mensagem'   => "Pac. {$nomePaciente} atualizada para {$cor}.",
                "evento"     => "triagem_atualizada",
                "triagem_id" => $t->id
            ]);


            return $t;
        }

        return $t->errors;
    }

    public function actionDelete($id)
{
    //  Verificação de Permissões
    if (!Yii::$app->user->can('admin') && !Yii::$app->user->can('enfermeiro')) {
        throw new ForbiddenHttpException("Apenas Administradores ou Enfermeiros podem apagar triagens.");
    }

    $t = Triagem::findOne($id);
    if (!$t) {
        throw new NotFoundHttpException("Triagem não encontrada.");
    }

    // Guardar o ID da Pulseira para apagar DEPOIS
    $pulseiraId = $t->pulseira_id;

    // Apagar a Triagem PRIMEIRO
    if (!$t->delete()) {
        // Se falhar (ex: por causa de Consultas associadas), mostra o erro real
        throw new \yii\web\ServerErrorHttpException("Erro ao apagar triagem. Verifique se existem consultas associadas.");
    }

    // Apagar a Pulseira 
    if ($pulseiraId) {
        $pulseira = Pulseira::findOne($pulseiraId);
        if ($pulseira) {
            $pulseira->delete();
        }
    }

    // Notificar via MQTT
    $this->safeMqttPublish("triagem/apagada/{$id}", [
        'titulo'     => 'Triagem Removida',       // Adicionado
        'mensagem'   => 'Uma triagem foi removida.', // Adicionado
        "evento"     => "triagem_apagada",
        "triagem_id" => $id
    ]);

    return ["status" => "success"];
}

    /**
     * Endpoint administrativo/estatístico
     * Pacientes devem usar actionIndex para ver o seu histórico
     */
    public function actionHistorico()
    {
        $user = Yii::$app->user;

        // Preparar a query base
        $query = Triagem::find()
            ->joinWith(['consulta'])
            ->with(['userprofile', 'pulseira'])
            ->where(['consulta.estado' => 'Encerrada']) // Mostra só as encerradas
            ->orderBy(['triagem.datatriagem' => SORT_DESC]);

        // Se for Paciente, aplica o filtro pelo perfil dele
        if ($user->can('paciente')) {
            $query->joinWith(['userprofile' => function ($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

        $triagens = $query->all();

        $result = [];
        foreach ($triagens as $t) {
            $result[] = [
                'id'                => $t->id,
                'datatriagem'       => $t->datatriagem,
                'motivoconsulta'    => $t->motivoconsulta,
                'queixaprincipal'   => $t->queixaprincipal,
                'consulta' => $t->consulta ? [
                    'id'     => $t->consulta->id,
                    'estado' => $t->consulta->estado,
                ] : null,
                'userprofile' => $t->userprofile ? [
                    'id'   => $t->userprofile->id,
                    'nome' => $t->userprofile->nome,
                ] : null,
                'pulseira' => $t->pulseira ? [
                    'id'         => $t->pulseira->id,
                    'prioridade' => $t->pulseira->prioridade,
                ] : null
            ];
        }

        return $result;
    }

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