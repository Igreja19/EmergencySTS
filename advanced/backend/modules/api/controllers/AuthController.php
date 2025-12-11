<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use common\models\User;
use common\models\UserProfile;
use yii\filters\auth\QueryParamAuth;

// MQTT
require_once __DIR__ . '/../mqtt/phpMQTT.php';
use backend\modules\api\mqtt\phpMQTT;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    // -------------------------------------------------------------
    // MQTT FUNCTION
    // -------------------------------------------------------------
    private function publishMqtt($topic, $payload)
    {
        $server = '127.0.0.1';
        $port = 1883;
        $clientId = 'emergencysts-auth-' . rand(1000,9999);

        $mqtt = new phpMQTT($server, $port, $clientId);

        if (!$mqtt->connect(true, NULL)) {
            return false;
        }

        $mqtt->publish($topic, $payload, 0);
        $mqtt->close();
        return true;
    }

    // -------------------------------------------------------------
    // BEHAVIORS
    // -------------------------------------------------------------
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
            'optional' => ['login', 'signup', 'validate'],
        ];

        return $behaviors;
    }

    // -------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------
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

        // Buscar a Role
        $role = Yii::$app->db->createCommand("
            SELECT item_name FROM auth_assignment WHERE user_id = :user_id LIMIT 1
        ")
            ->bindValue(':user_id', $user->id)
            ->queryScalar();

        $profile = UserProfile::findOne(['user_id' => $user->id]);

        // MQTT — login efetuado
        $this->publishMqtt(
            "user/login/" . $user->id,
            json_encode([
                "evento" => "user_login",
                "user_id" => $user->id,
                "username" => $user->username,
                "hora" => date('Y-m-d H:i:s')
            ])
        );

        return [
            'status' => true,
            'message' => 'Login efetuado com sucesso.',
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

    // -------------------------------------------------------------
    // SIGNUP
    // -------------------------------------------------------------
    public function actionSignup()
    {
        $data = Yii::$app->request->post();

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            throw new BadRequestHttpException("Faltam dados obrigatórios (username, email, password).");
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $user = new User();
            $user->username = $data['username'];
            $user->email = $data['email'];
            $user->setPassword($data['password']);
            $user->generateAuthKey();
            $user->status = 10;

            if (!$user->save()) {
                throw new \Exception("Erro no utilizador: " . json_encode($user->errors));
            }

            // Atribuir role paciente
            $auth = Yii::$app->authManager;
            $rolePac = $auth->getRole('paciente');
            if ($rolePac) {
                $auth->assign($rolePac, $user->id);
            }

            // Criar perfil
            $profile = new UserProfile();
            $profile->user_id = $user->id;

            $profileData = $data['profile'] ?? [];

            $profile->nome = $profileData['nome'] ?? $user->username;
            $profile->email = $user->email;
            $profile->nif = $profileData['nif'] ?? null;
            $profile->sns = $profileData['sns'] ?? null;
            $profile->telefone = $profileData['telefone'] ?? null;
            $profile->genero = $profileData['genero'] ?? null;
            $profile->datanascimento = $profileData['datanascimento'] ?? null;

            if (!$profile->save()) {
                throw new \Exception("Erro no perfil: " . json_encode($profile->errors));
            }

            $transaction->commit();

            // MQTT — user criado
            $this->publishMqtt(
                "user/criado/" . $user->id,
                json_encode([
                    "evento" => "user_criado",
                    "user_id" => $user->id,
                    "username" => $user->username,
                    "email" => $user->email,
                    "nome" => $profile->nome,
                    "hora" => date('Y-m-d H:i:s'),
                ])
            );

            return [
                'status' => true,
                'message' => 'Conta criada com sucesso.',
                'data' => [
                    'user_id' => $user->id,
                    'userprofile_id' => $profile->id,
                    'username' => $user->username,
                    'token' => $user->auth_key
                ]
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 422;
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // -------------------------------------------------------------
    // VALIDATE TOKEN
    // -------------------------------------------------------------
    public function actionValidate($auth_key)
    {
        $user = User::findOne(['auth_key' => $auth_key]);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Token inválido ou expirado.',
            ];
        }

        return [
            'status' => true,
            'message' => 'Token válido.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email
            ]
        ];
    }
}
