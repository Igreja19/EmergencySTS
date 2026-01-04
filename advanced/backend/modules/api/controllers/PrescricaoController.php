<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use backend\modules\api\controllers\BaseActiveController;

use common\models\Prescricao;
use common\models\Prescricaomedicamento;
use common\models\Consulta;
use common\models\Medicamento;

class PrescricaoController extends BaseActiveController
{
    public $modelClass = 'common\models\Prescricao';
    public $enableCsrfValidation = false;

    // NOTA: behaviors() removido porque herda do BaseActiveController

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    // LISTAR PRESCRIÇÕES
    public function actionIndex()
    {
        $user = Yii::$app->user;
        $query = Prescricao::find();

        // --- SEGURANÇA: Se for Paciente, filtra apenas as dele ---
        if ($user->can('paciente')) {
            // A query junta as tabelas para encontrar o dono da prescrição:
            // Prescricao -> Consulta -> Triagem -> UserProfile -> User (ID logado)
            $query->joinWith(['consulta.triagem.userprofile' => function($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

        $prescricoes = $query->orderBy(['dataprescricao' => SORT_DESC])->all();

        $data = [];
        foreach ($prescricoes as $p) {
            $medicamentos = [];

            if ($p->getPrescricaomedicamentos()->exists()) {
                foreach ($p->prescricaomedicamentos as $pm) {
                    $medicamentos[] = $pm->medicamento->nome . ' (' . $pm->posologia . ')';
                }
            }

            // Tenta obter o nome do médico, se a coluna existir ou estiver relacionada
            $medicoNome = $p->consulta->medico_nome ?? 'Médico';

            $data[] = [
                'id'           => $p->id,
                'data'         => $p->dataprescricao,
                'medico'       => $medicoNome,
                'medicamentos' => $medicamentos,
                'consulta_id'  => $p->consulta_id,
            ];
        }

        return ['status' => 'success', 'total' => count($data), 'data' => $data];
    }

    // VER UMA PRESCRIÇÃO
    public function actionView($id)
    {
        $prescricao = Prescricao::findOne($id);
        if (!$prescricao) {
            throw new NotFoundHttpException("Prescrição não encontrada.");
        }

        // --- SEGURANÇA: Verificar se a receita pertence ao paciente ---
        if (Yii::$app->user->can('paciente')) {
            // Percorre a relação para chegar ao ID do utilizador dono da triagem
            $donoId = $prescricao->consulta->triagem->userprofile->user_id ?? null;
            
            if ($donoId != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta prescrição.");
            }
        }

        $listaMedicamentos = [];
        if ($prescricao->getPrescricaomedicamentos()->exists()) {
            foreach ($prescricao->prescricaomedicamentos as $pm) {
                $listaMedicamentos[] = [
                    'nome'      => $pm->medicamento->nome,
                    'dosagem'   => $pm->medicamento->dosagem,
                    'posologia' => $pm->posologia,
                ];
            }
        }

        return [
            'status' => 'success',
            'data'   => [
                'id'          => $prescricao->id,
                'data'        => $prescricao->dataprescricao,
                'observacoes' => $prescricao->observacoes,
                'medicamentos'=> $listaMedicamentos,
            ],
        ];
    }

    // CRIAR (Apenas Médicos/Admin)
    public function actionCreate()
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem criar prescrições.");
        }

        $data = Yii::$app->request->post();
        if (empty($data['consulta_id'])) {
            throw new BadRequestHttpException("Falta consulta_id.");
        }

        $consulta = Consulta::findOne($data['consulta_id']);
        if (!$consulta) {
            throw new NotFoundHttpException("Consulta não encontrada.");
        }

        $tx = Yii::$app->db->beginTransaction();

        try {
            $prescricao = new Prescricao();
            $prescricao->consulta_id   = $consulta->id;
            $prescricao->dataprescricao= date('Y-m-d H:i:s');
            $prescricao->observacoes   = $data['observacoes'] ?? '';

            if (!$prescricao->save()) {
                throw new \Exception("Erro ao criar prescrição.");
            }

            if (!empty($data['medicamentos']) && is_array($data['medicamentos'])) {
                foreach ($data['medicamentos'] as $item) {
                    // Procura medicamento existente ou cria novo
                    $medicamento = Medicamento::findOne([
                        'nome'    => $item['nome'],
                        'dosagem' => $item['dosagem'],
                    ]);

                    if (!$medicamento) {
                        $medicamento = new Medicamento();
                        $medicamento->nome    = $item['nome'];
                        $medicamento->dosagem = $item['dosagem'];
                        $medicamento->save();
                    }

                    $linha = new Prescricaomedicamento();
                    $linha->prescricao_id  = $prescricao->id;
                    $linha->medicamento_id = $medicamento->id;
                    $linha->posologia      = $item['posologia'];
                    $linha->save();
                }
            }

            $tx->commit();

            // MQTT Seguro
            $this->safeMqttPublish("prescricao/criada/{$prescricao->id}", [
                'evento'       => 'prescricao_criada',
                'prescricao_id'=> $prescricao->id,
                'consulta_id'  => $prescricao->consulta_id,
                'hora'         => date('Y-m-d H:i:s'),
            ]);

            return ['status' => 'success', 'data' => $prescricao];

        } catch (\Exception $e) {
            $tx->rollBack();
            Yii::$app->response->statusCode = 422;
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // DELETE (Apenas Médicos/Admin)
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos/admin.");
        }

        $prescricao = Prescricao::findOne($id);
        if (!$prescricao) {
            throw new NotFoundHttpException("Não encontrada.");
        }

        Prescricaomedicamento::deleteAll(['prescricao_id' => $id]);
        $prescricao->delete();

        // MQTT Seguro
        $this->safeMqttPublish("prescricao/apagada/{$id}", [
            'evento'       => 'prescricao_apagada',
            'prescricao_id'=> $id,
            'hora'         => date('Y-m-d H:i:s'),
        ]);

        return ['status' => 'success', 'message' => 'Prescrição anulada.'];
    }

    /**
     * Função auxiliar interna para MQTT
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