<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

require_once __DIR__ . '/../mqtt/phpMQTT.php';
use backend\modules\api\mqtt\phpMQTT;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;
    public $layout = false;

    private function publishMqtt($topic, $payload)
    {
        $server = '127.0.0.1';
        $port = 1883;
        $clientId = 'emergencysts-user-' . rand(1000,9999);

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
        $behaviors['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;
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

    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update') {
            if (Yii::$app->user->can('admin')) return;

            if ($model && $model->user_id == Yii::$app->user->id) return;

            throw new ForbiddenHttpException("Não tem permissão para editar este perfil.");
        }

        if ($action === 'delete') {
            if (!Yii::$app->user->can('admin')) {
                throw new ForbiddenHttpException("Apenas administradores podem apagar utilizadores.");
            }
        }
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem criar utilizadores.");
        }

        $params = Yii::$app->request->getBodyParams();

        $user = new \common\models\User();
        $user->username = $params['username'];
        $user->email = $params['email'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();
        $user->status = 10;

        if (!$user->save()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $user->getErrors()];
        }

        $profile = new \common\models\UserProfile();
        $profile->user_id = $user->id;
        $profile->nome = $params['nome'];
        $profile->email = $user->email;
        $profile->nif = $params['nif'];
        $profile->sns = $params['sns'];
        $profile->datanascimento = $params['datanascimento'];
        $profile->genero = $params['genero'];
        $profile->telefone = $params['telefone'];

        if (!$profile->save()) {
            $user->delete();
            Yii::$app->response->statusCode = 422;
            return ['errors' => $profile->getErrors()];
        }

        // MQTT — user criado
        $this->publishMqtt(
            "user/criado/" . $user->id,
            json_encode([
                "evento" => "user_criado",
                "user_id" => $user->id,
                "nome" => $profile->nome,
                "email" => $profile->email,
                "role" => $params['role'] ?? 'paciente',
                "hora" => date('Y-m-d H:i:s'),
            ])
        );

        Yii::$app->response->statusCode = 201;
        return $profile;
    }

    public function actionIndex()
    {
        if (Yii::$app->user->can('admin')) {
            return \common\models\UserProfile::find()->asArray()->all();
        }

        $id = Yii::$app->user->id;
        $profile = \common\models\UserProfile::find()->where(['user_id' => $id])->asArray()->one();

        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        return $profile;
    }

    public function actionView($id)
    {
        $logged = Yii::$app->user->id;

        $profile = \common\models\UserProfile::find()->where(['id' => $id])->asArray()->one();

        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        if (!Yii::$app->user->can('admin') && $profile['user_id'] != $logged) {
            throw new ForbiddenHttpException("Não tem permissão.");
        }

        return $profile;
    }

    public function actionUpdate($id)
    {
        $model = \common\models\UserProfile::findOne($id);
        if (!$model) throw new NotFoundHttpException("Perfil não encontrado.");

        $this->checkAccess('update', $model);

        $model->load(Yii::$app->request->post(), '');
        $model->save();

        // MQTT — user atualizado
        $this->publishMqtt(
            "user/atualizado/" . $model->user_id,
            json_encode([
                "evento" => "user_atualizado",
                "user_id" => $model->user_id,
                "dados" => $model->attributes,
                "hora" => date('Y-m-d H:i:s'),
            ])
        );

        return $model;
    }

    public function actionDelete($id)
    {
        $model = \common\models\UserProfile::findOne($id);
        if (!$model) throw new NotFoundHttpException("Perfil não encontrado.");

        $this->checkAccess('delete', $model);

        $userId = $model->user_id;
        $model->delete();

        // MQTT — user apagado
        $this->publishMqtt(
            "user/apagado/" . $userId,
            json_encode([
                "evento" => "user_apagado",
                "user_id" => $userId,
                "hora" => date('Y-m-d H:i:s'),
            ])
        );

        return ['status' => 'success'];
    }
}
