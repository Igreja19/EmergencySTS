<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

use common\models\User;
use common\models\UserProfile;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;
    public $layout = false;

    public function behaviors()
    {
        $b = parent::behaviors();
        unset($b['authenticator']);

        $b['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;
        $b['authenticator'] = [
            'class'      => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $b;
    }

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update') {
            if (Yii::$app->user->can('admin')) {
                return;
            }

            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }

            throw new ForbiddenHttpException("Não tem permissão para editar este perfil.");
        }

        if ($action === 'delete') {
            if (!Yii::$app->user->can('admin')) {
                throw new ForbiddenHttpException("Apenas administradores podem apagar utilizadores.");
            }
        }
    }

    // Criar utilizador via API Admin
    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem criar utilizadores.");
        }

        $params = Yii::$app->request->getBodyParams();

        $user = new User();
        $user->username = $params['username'];
        $user->email    = $params['email'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();
        $user->status   = 10;

        if (!$user->save()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $user->getErrors()];
        }

        $profile = new UserProfile();
        $profile->user_id       = $user->id;
        $profile->nome          = $params['nome'];
        $profile->email         = $user->email;
        $profile->nif           = $params['nif'];
        $profile->sns           = $params['sns'];
        $profile->datanascimento= $params['datanascimento'];
        $profile->genero        = $params['genero'];
        $profile->telefone      = $params['telefone'];

        if (!$profile->save()) {
            $user->delete();
            Yii::$app->response->statusCode = 422;
            return ['errors' => $profile->getErrors()];
        }

        $auth = Yii::$app->authManager;
        $roleName = $params['role'] ?? 'paciente';
        $role = $auth->getRole($roleName);
        if ($role) {
            $auth->assign($role, $user->id);
        }

        // MQTT – user criado via backend
        Yii::$app->mqtt->publish(
            "user/criado/{$user->id}",
            json_encode([
                'evento'   => 'user_criado',
                'user_id'  => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
                'nome'     => $profile->nome,
                'role'     => $roleName,
                'hora'     => date('Y-m-d H:i:s'),
            ])
        );

        Yii::$app->response->statusCode = 201;
        return $profile;
    }

    // GET /api/user
    public function actionIndex()
    {
        if (Yii::$app->user->can('admin')) {
            $profiles = UserProfile::find()->asArray()->all();
            return [
                'Total de perfis' => count($profiles),
                'Data'            => $profiles,
            ];
        }

        $loggedId = Yii::$app->user->id;
        $profile = UserProfile::find()->where(['user_id' => $loggedId])->asArray()->one();

        if (!$profile) {
            throw new NotFoundHttpException("Não foi encontrado um perfil para o utilizador logado.");
        }

        return $profile;
    }

    // GET /api/user/{id}
    public function actionView($id)
    {
        $loggedId = Yii::$app->user->id;
        $profile = UserProfile::find()->where(['id' => $id])->asArray()->one();

        if (!$profile) {
            throw new NotFoundHttpException("Perfil com ID {$id} não encontrado.");
        }

        if (!Yii::$app->user->can('admin') && $profile['user_id'] != $loggedId) {
            throw new ForbiddenHttpException("Não tem permissão para ver este perfil.");
        }

        return $profile;
    }
}
