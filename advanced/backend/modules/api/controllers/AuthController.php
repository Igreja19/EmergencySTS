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

        // ✅ Força JSON mesmo se pedirem HTML
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // ✅ Autenticação via parâmetro "auth_key"
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key', // URL param ex: ?auth_key=abc123
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

        // ✅ Gera nova auth_key (se quiseres renovar a cada login)
        $user->generateAuthKey();
        $user->save(false);

        return [
            'status' => 'success',
            'message' => 'Login efetuado com sucesso.',
            'user_id' => $user->id,
            'username' => $user->username,
            'auth_key' => $user->auth_key, // 🔑 Token que o Android usará
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
            ],
        ];
    }
}
