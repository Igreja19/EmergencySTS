<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use common\models\User;
use yii\filters\auth\QueryParamAuth;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // 🔹 força resposta JSON
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // 🔹 autenticação via parâmetro ?auth_key=XYZ (opcional)
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
            'optional' => ['login', 'validate'], // permite login sem token
        ];

        return $behaviors;
    }

    // ✅ POST /api/auth/login
    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post();
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return [
                'status' => false,
                'message' => 'Credenciais em falta.',
                'data' => null,
            ];
        }

        $user = User::findByUsername($username);

        if (!$user || !$user->validatePassword($password)) {
            return [
                'status' => false,
                'message' => 'Utilizador ou palavra-passe incorretos.',
                'data' => null,
            ];
        }

        // 🔹 Buscar role real a partir da tabela auth_assignment
        $role = Yii::$app->db->createCommand("
            SELECT item_name
            FROM auth_assignment
            WHERE user_id = :user_id
            LIMIT 1
        ")
            ->bindValue(':user_id', $user->id)
            ->queryScalar();

        if (!$role) {
            $role = 'paciente'; // fallback
        }

        return [
            'status' => true,
            'message' => 'Login efetuado com sucesso.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $role,
                'auth_key' => $user->auth_key,
            ],
        ];
    }

    // ✅ GET /api/auth/validate?auth_key=XYZ
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
