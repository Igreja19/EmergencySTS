<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;

use common\models\Pulseira;
use common\models\UserProfile;

require_once __DIR__ . '/../mqtt/phpMQTT.php';
use backend\modules\api\mqtt\phpMQTT;

class PulseiraController extends ActiveController
{
    public $modelClass = 'common\models\Pulseira';
    public $enableCsrfValidation = false;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

    private function publishMqtt($topic, $payload)
    {
        $server = '127.0.0.1';
        $port = 1883;
        $clientId = 'emergencysts-pulseira-' . rand(1000,9999);

        $mqtt = new phpMQTT($server, $port, $clientId);

        if (!$mqtt->connect(true, NULL)) {
            return false;
        }

        $mqtt->publish($topic, $payload, 0);
        $mqtt->close();

        return true;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user;
        $query = Pulseira::find();

        if ($status = Yii::$app->request->get('status')) {
            $query->andWhere(['status' => $status]);
        }

        if ($prioridade = Yii::$app->request->get('prioridade')) {
            $query->andWhere(['prioridade' => $prioridade]);
        }

        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }
            $query->andWhere(['userprofile_id' => $profile->id]);
        }

        $query->orderBy(['tempoentrada' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    public function actionView($id)
    {
        $pulseira = Pulseira::findOne($id);

        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $user = Yii::$app->user;
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if ($pulseira->userprofile_id != $profile->id) {
                throw new ForbiddenHttpException("Sem permissão.");
            }
        }

        return $pulseira;
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais de saúde.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $pulseira->load(Yii::$app->request->post(), '');

        if ($pulseira->save()) {

            // MQTT — pulseira atualizada
            $this->publishMqtt(
                "pulseira/atualizada/" . $pulseira->id,
                json_encode([
                    "evento" => "pulseira_atualizada",
                    "pulseira_id" => $pulseira->id,
                    "prioridade" => $pulseira->prioridade,
                    "status" => $pulseira->status,
                    "userprofile_id" => $pulseira->userprofile_id,
                    "hora" => date('Y-m-d H:i:s'),
                ])
            );

            return $pulseira;
        }

        return ['status' => 'error', 'errors' => $pulseira->getErrors()];
    }

    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $pulseiraId = $pulseira->id;
        $userProfileId = $pulseira->userprofile_id;

        $pulseira->delete();

        // MQTT — pulseira eliminada
        $this->publishMqtt(
            "pulseira/apagada/" . $pulseiraId,
            json_encode([
                "evento" => "pulseira_apagada",
                "pulseira_id" => $pulseiraId,
                "userprofile_id" => $userProfileId,
                "hora" => date('Y-m-d H:i:s'),
            ])
        );

        return ['status' => 'success'];
    }
}
