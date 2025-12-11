<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\filters\auth\QueryParamAuth;

use common\models\User;
use common\models\UserProfile;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $b = parent::behaviors();

        $b['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        $b['authenticator'] = [
            'class'      => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
            'optional'   => ['login', 'signup', 'validate'],
        ];

        return $b;
    }

    // LOGIN
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
            return ['status' => false, 'message' => 'Utilizador ou palavra-passe incorretos.', 'data' => null];
        }

        $role = Yii::$app->db
            ->createCommand("SELECT item_name FROM auth_assignment WHERE user_id = :uid LIMIT 1")
            ->bindValue(':uid', $user->id)
            ->queryScalar();

        $profile = UserProfile::findOne(['user_id' => $user->id]);

        return [
            'status'  => true,
            'message' => 'Login efetuado com sucesso.',
            'data'    => [
                'user_id'        => $user->id,
                'userprofile_id' => $profile ? $profile->id : null,
                'username'       => $user->username,
                'email'          => $user->email,
                'role'           => $role ?? 'paciente',
                'token'          => $user->auth_key,
            ],
        ];
    }

    // SIGNUP
    public function actionSignup()
    {
        $data = Yii::$app->request->post();

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            throw new BadRequestHttpException("Faltam dados obrigatórios (username, email, password).");
        }

        $tx = Yii::$app->db->beginTransaction();

        try {
            $user = new User();
            $user->username = $data['username'];
            $user->email    = $data['email'];
            $user->setPassword($data['password']);
            $user->generateAuthKey();
            $user->status   = 10;

            if (!$user->save()) {
                throw new \Exception("Erro no utilizador: " . json_encode($user->errors));
            }

            // role (default: paciente)
            $auth = Yii::$app->authManager;
            $roleName = $data['role'] ?? 'paciente';
            $role = $auth->getRole($roleName);
            if ($role) {
                $auth->assign($role, $user->id);
            }

            // perfil
            $profileData = $data['profile'] ?? [];
            $profile = new UserProfile();
            $profile->user_id       = $user->id;
            $profile->nome          = $profileData['nome'] ?? $user->username;
            $profile->email         = $user->email;
            $profile->nif           = $profileData['nif'] ?? null;
            $profile->sns           = $profileData['sns'] ?? null;
            $profile->telefone      = $profileData['telefone'] ?? null;
            $profile->genero        = $profileData['genero'] ?? null;
            $profile->datanascimento= $profileData['datanascimento'] ?? null;

            if (!$profile->save()) {
                throw new \Exception("Erro no perfil: " . json_encode($profile->errors));
            }

            $tx->commit();

            // MQTT – utilizador criado
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

            return [
                'status'  => true,
                'message' => 'Conta criada com sucesso.',
                'data'    => [
                    'user_id'        => $user->id,
                    'userprofile_id' => $profile->id,
                    'username'       => $user->username,
                    'token'          => $user->auth_key,
                ],
            ];

        } catch (\Exception $e) {
            $tx->rollBack();
            Yii::$app->response->statusCode = 422;
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // VALIDATE TOKEN
    public function actionValidate($auth_key)
    {
        $user = User::findOne(['auth_key' => $auth_key]);
        if (!$user) {
            return ['status' => false, 'message' => 'Token inválido ou expirado.'];
        }

        return [
            'status'  => true,
            'message' => 'Token válido.',
            'data'    => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ],
        ];
    }
}
