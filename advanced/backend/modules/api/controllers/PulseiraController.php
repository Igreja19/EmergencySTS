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

    // NOTA: behaviors() removido porque herda do BaseActiveController

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    // GET /api/pulseira
    public function actionIndex()
    {
        // O BaseActiveController já garante que quem acede é Admin/Médico/Enfermeiro.
        // Por isso, removemos a lógica de filtrar "pelo perfil do paciente".

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

        // Não é necessário validar se a pulseira pertence ao user,
        // pois Pacientes estão bloqueados e Profissionais podem ver tudo.

        return $pulseira;
    }

    // PUT /api/pulseira/{id}
    public function actionUpdate($id)
    {
        // A verificação de permissão é feita pelo BaseActiveController (Admin/Med/Enf).

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