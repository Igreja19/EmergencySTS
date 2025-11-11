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

        // ✅ força JSON mesmo se pedirem HTML
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // ✅ autenticação via parâmetro ?auth_key=XYZ
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
            'optional' => ['login', 'validate'], // ❗ permite login sem token
        ];

        return $behaviors;
    }

    // ✅ POST /api/auth/login
    public function actionLogin()
    {
        $data = Yii::$app->request->post();
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            throw new UnauthorizedHttpException('Credenciais em falta.');
        }

        $user = User::findByUsername($username);

        if (!$user || !$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('Utilizador ou palavra-passe incorretos.');
        }

        // 🔹 apenas devolve a auth_key existente
        return [
            'status' => 'success',
            'message' => 'Login efetuado com sucesso.',
            'user_id' => $user->id,
            'username' => $user->username,
            'auth_key' => $user->auth_key, // 🔑 vai buscar o que já está guardado na BD
        ];
    }

    // ✅ GET /api/auth/validate?auth_key=XYZ
    public function actionValidate($auth_key)
    {
        $user = User::findOne(['auth_key' => $auth_key]);

        if (!$user) {
            throw new UnauthorizedHttpException('Token inválido ou expirado.');
        }

        return [
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ];
    }
}
