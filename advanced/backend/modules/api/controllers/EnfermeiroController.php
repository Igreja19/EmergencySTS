<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\User;
use common\models\UserProfile;

// MQTT
require_once __DIR__ . '/../mqtt/phpMQTT.php';
use backend\modules\api\mqtt\phpMQTT;

class EnfermeiroController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    // ---------------------------------------------------------
    // MQTT FUNCTION
    // ---------------------------------------------------------
    private function publishMqtt($topic, $payload)
    {
        $server = '127.0.0.1';
        $port = 1883;
        $clientId = 'emergencysts-enfermeiro-' . rand(1000,9999);

        $mqtt = new phpMQTT($server, $port, $clientId);

        if (!$mqtt->connect(true, NULL)) {
            return false;
        }

        $mqtt->publish($topic, $payload, 0);
        $mqtt->close();
        return true;
    }

    // ---------------------------------------------------------
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);
        $behaviors['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;

        // autenticação via ?auth_key=
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    // ---------------------------------------------------------
    public function checkAccess($action, $model = null, $params = [])
    {
        if (Yii::$app->user->can('admin')) {
            return;
        }

        // ENFERMEIRO só vê o seu próprio perfil
        if ($action === 'view' || $action === 'perfil') {
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            throw new ForbiddenHttpException("Sem permissão para aceder a este perfil.");
        }
    }

    // ---------------------------------------------------------
    /**
     * GET /api/enfermeiro/perfil
     */
    public function actionPerfil()
    {
        $userId = Yii::$app->user->id;

        $perfil = UserProfile::find()
            ->where(['user_id' => $userId])
            ->asArray()
            ->one();

        if (!$perfil) {
            throw new NotFoundHttpException("Perfil do enfermeiro não encontrado.");
        }

        // MQTT — enfermeiro carregou o próprio perfil
        $this->publishMqtt(
            "enfermeiro/perfil/" . $perfil['id'],
            json_encode([
                "evento" => "enfermeiro_perfil",
                "enfermeiro_id" => $perfil['id'],
                "nome" => $perfil['nome'],
                "hora" => date('Y-m-d H:i:s')
            ])
        );

        return $perfil;
    }

    // ---------------------------------------------------------
    /**
     * GET /api/enfermeiro/{id}
     */
    public function actionView($id)
    {
        $model = UserProfile::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException("Enfermeiro não encontrado.");
        }

        $this->checkAccess('view', $model);

        // MQTT — enfermeiro visualizado por admin ou por si próprio
        $this->publishMqtt(
            "enfermeiro/view/" . $model->id,
            json_encode([
                "evento" => "enfermeiro_view",
                "enfermeiro_id" => $model->id,
                "nome" => $model->nome,
                "hora" => date('Y-m-d H:i:s')
            ])
        );

        return $model;
    }
}
