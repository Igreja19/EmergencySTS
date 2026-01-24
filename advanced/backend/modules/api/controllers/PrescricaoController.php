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

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user;

        $query = Prescricao::find()
            ->with(['consulta', 'prescricaomedicamentos.medicamento']);

        // Segurança: Paciente só vê prescrições ligadas à sua Triagem
        if ($user->can('paciente')) {
            $query->joinWith(['consulta.triagem.userprofile' => function ($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

        $prescricoes = $query->orderBy(['dataprescricao' => SORT_DESC])->all();

        $data = [];
        foreach ($prescricoes as $p) {
            $medicamentos = [];
            if ($p->prescricaomedicamentos) {
                foreach ($p->prescricaomedicamentos as $pm) {
                    $medicamentos[] = $pm->medicamento->nome . ' (' . $pm->posologia . ')';
                }
            }

            $data[] = [
                'id'            => $p->id,
                'data'          => $p->dataprescricao,
                'medico'        => $p->consulta->medico_nome ?? 'Médico',
                'medicamentos'  => $medicamentos,
                'consulta_id'   => $p->consulta_id,
            ];
        }

        return ['status' => 'success', 'total' => count($data), 'data' => $data];
    }

    public function actionView($id)
    {
        $prescricao = Prescricao::find()
            ->where(['id' => $id])
            ->with(['prescricaomedicamentos.medicamento', 'consulta'])
            ->one();

        if (!$prescricao) {
            throw new NotFoundHttpException("Prescrição não encontrada.");
        }

        if (Yii::$app->user->can('paciente')) {
            $donoId = $prescricao->consulta->triagem->userprofile->user_id ?? null;

            if ($donoId != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta prescrição.");
            }
        }

        $listaMedicamentos = [];
        if ($prescricao->prescricaomedicamentos) {
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
                'medico'      => $prescricao->consulta->medico_nome ?? 'N/A',
                'medicamentos'=> $listaMedicamentos,
            ],
        ];
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem criar prescrições.");
        }

        $data = Yii::$app->request->post();

        if (empty($data['consulta_id'])) {
            throw new BadRequestHttpException("Falta o ID da consulta.");
        }

        $consulta = Consulta::findOne($data['consulta_id']);
        if (!$consulta) {
            throw new NotFoundHttpException("Consulta não encontrada.");
        }

        $tx = Yii::$app->db->beginTransaction();

        try {
            $prescricao = new Prescricao();
            $prescricao->consulta_id    = $consulta->id;
            $prescricao->dataprescricao = date('Y-m-d H:i:s');
            $prescricao->observacoes    = $data['observacoes'] ?? '';

            if (!$prescricao->save()) {
                throw new \Exception("Erro ao guardar cabeçalho da prescrição.");
            }

            if (!empty($data['medicamentos']) && is_array($data['medicamentos'])) {
                foreach ($data['medicamentos'] as $item) {

                    // Busca medicamento existente ou cria novo
                    $medicamento = Medicamento::findOne([
                        'nome'    => $item['nome'],
                        'dosagem' => $item['dosagem'],
                    ]);

                    if (!$medicamento) {
                        $medicamento = new Medicamento();
                        $medicamento->nome    = $item['nome'];
                        $medicamento->dosagem = $item['dosagem'];
                        if (!$medicamento->save()) {
                            throw new \Exception("Erro ao criar medicamento: " . $item['nome']);
                        }
                    }

                    $linha = new Prescricaomedicamento();
                    $linha->prescricao_id  = $prescricao->id;
                    $linha->medicamento_id = $medicamento->id;
                    $linha->posologia      = $item['posologia'];

                    if (!$linha->save()) {
                        throw new \Exception("Erro ao guardar linha do medicamento.");
                    }
                }
            }

            $tx->commit();

            $this->safeMqttPublish("prescricao/criada/{$prescricao->id}", [
                'evento'        => 'prescricao_criada',
                'prescricao_id' => $prescricao->id,
                'consulta_id'   => $prescricao->consulta_id,
                'hora'          => date('Y-m-d H:i:s'),
            ]);

            return ['status' => 'success', 'data' => $prescricao];

        } catch (\Exception $e) {
            $tx->rollBack();
            Yii::$app->response->statusCode = 422;
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Sem permissão.");
        }

        $prescricao = Prescricao::findOne($id);
        if (!$prescricao) {
            throw new NotFoundHttpException("Prescrição não encontrada.");
        }

        Prescricaomedicamento::deleteAll(['prescricao_id' => $id]);
        $prescricao->delete();

        $this->safeMqttPublish("prescricao/apagada/{$id}", [
            'evento'        => 'prescricao_apagada',
            'prescricao_id' => $id,
            'hora'          => date('Y-m-d H:i:s'),
        ]);

        return ['status' => 'success', 'message' => 'Prescrição eliminada.'];
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