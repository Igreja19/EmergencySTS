<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use common\models\User;
use yii\filters\auth\QueryParamAuth;
use common\models\UserProfile;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
            'optional' => ['login', 'validate'],
        ];
        return $behaviors;
    }

    // POST /api/auth/login
    public function actionLogin()
    {
        $data = Yii::$app->request->post();
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        if (!$username || !$password) {
            return ['status' => false, 'message' => 'Credenciais em falta.', 'data' => null];
        }
        $user = User::findByUsername($username);

        if (!$user || !$user->validatePassword($password)) {
            return ['status' => false, 'message' => 'Dados incorretos.', 'data' => null];
        }

        // buscar a Role
        $role = Yii::$app->db->createCommand("SELECT item_name FROM auth_assignment WHERE user_id = :user_id LIMIT 1")
            ->bindValue(':user_id', $user->id)
            ->queryScalar();

        // buscar o Perfil
        $profile = UserProfile::findOne(['user_id' => $user->id]);
        return [
            'status' => true,
            'message' => 'Login com sucesso.',
            'data' => [
                'user_id' => $user->id,
                'userprofile_id' => $profile ? $profile->id : null,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $role ?? 'paciente',
                'token' => $user->auth_key,
            ],
        ];
    }

    // GET /api/auth/validate?auth_key=XYZ
    public function actionValidate($auth_key)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = User::findOne(['auth_key' => $auth_key]);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Token inválido ou expirado.',
                'data' => null,
            ];
        }

        return [
            'status' => true,
            'message' => 'Token válido.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role ?? 'paciente',
            ],
        ];
    }
}
