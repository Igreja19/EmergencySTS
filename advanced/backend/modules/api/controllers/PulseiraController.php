<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;
use backend\modules\api\controllers\BaseActiveController;

use common\models\Pulseira;
use common\models\UserProfile;

class PulseiraController extends BaseActiveController
{
    public $modelClass = 'common\models\Pulseira';
    public $enableCsrfValidation = false;

    // Envelope "data": [...]
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    // GET /api/pulseira
    public function actionIndex()
    {
        // SEGURANÇA: Bloquear pacientes de ver lista de pulseiras (dados internos)
        if (Yii::$app->user->can('paciente')) {
            throw new ForbiddenHttpException("Acesso reservado a staff.");
        }

        $query = Pulseira::find();

        // filtros
        $status = Yii::$app->request->get('status');
        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $prioridade = Yii::$app->request->get('prioridade');
        if ($prioridade) {
            $query->andWhere(['prioridade' => $prioridade]);
        }

        $query->orderBy(['tempoentrada' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    // GET /api/pulseira/{id}
    public function actionView($id)
    {
        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        // SEGURANÇA: Paciente só vê a sua própria pulseira
        if (Yii::$app->user->can('paciente')) {
            if ($pulseira->userprofile->user_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta pulseira.");
            }
        }

        return $pulseira;
    }

    // PUT /api/pulseira/{id}
    public function actionUpdate($id)
    {
        // SEGURANÇA CRÍTICA: Impedir pacientes de editar pulseiras
        // Agora que o BaseActiveController deixa passar pacientes, temos de bloquear aqui explicitamente.
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais podem alterar pulseiras.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $data = Yii::$app->request->post();
        $pulseira->load($data, '');

        if ($pulseira->save()) {

            // MQTT Seguro
            $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
            if ($mqttEnabled && isset(Yii::$app->mqtt)) {
                try {
                    Yii::$app->mqtt->publish(
                        "pulseira/atualizada/{$pulseira->id}",
                        json_encode([
                            'evento'        => 'pulseira_atualizada',
                            'pulseira_id'   => $pulseira->id,
                            'prioridade'    => $pulseira->prioridade,
                            'status'        => $pulseira->status,
                            'userprofile_id'=> $pulseira->userprofile_id,
                            'hora'          => date('Y-m-d H:i:s'),
                        ])
                    );
                } catch (\Exception $e) {
                    Yii::error("Erro MQTT Pulseira Update: " . $e->getMessage());
                }
            }

            return $pulseira;
        }

        return [
            'status' => 'error',
            'errors' => $pulseira->getErrors(),
        ];
    }

    // DELETE /api/pulseira/{id}
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Não encontrada.");
        }

        $pulseira->delete();

        // MQTT Seguro
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish(
                    "pulseira/apagada/{$id}",
                    json_encode([
                        'evento'      => 'pulseira_apagada',
                        'pulseira_id' => $id,
                        'hora'        => date('Y-m-d H:i:s'),
                    ])
                );
            } catch (\Exception $e) {
                Yii::error("Erro MQTT Pulseira Delete: " . $e->getMessage());
            }
        }

        return ['status' => 'success'];
    }
}