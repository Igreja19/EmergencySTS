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
            'optional' => ['login', 'validate','signup'],
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

    // POST /api/auth/signup
    public function actionSignup()
    {
        $data = Yii::$app->request->post();
        
        // Validar campos mínimos
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return ['status' => false, 'message' => 'Faltam dados obrigatórios (username, email, password).'];
        }
o
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Criar User
            $user = new User();
            $user->username = $data['username'];
            $user->email = $data['email'];
            $user->setPassword($data['password']);
            $user->generateAuthKey();
            $user->status = 10

            if (!$user->save()) {
                throw new \Exception('Erro ao criar conta: ' . json_encode($user->getErrors()));
            }

            //.Atribuir Role 'paciente'
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('paciente');
            $auth->assign($authorRole, $user->id);

            // Criar UserProfile 
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            // Campos obrigatórios do seu UserProfile
            $profile->nome = $data['nome'] ?? $user->username; 
            $profile->email = $user->email;
            $profile->nif = $data['nif'] ?? null;
            $profile->sns = $data['sns'] ?? null;
            $profile->datanascimento = $data['datanascimento'] ?? null;
            $profile->genero = $data['genero'] ?? null;
            $profile->telefone = $data['telefone'] ?? null;
            
            // Nota: Se a sua BD exige estes campos NOT NULL, 
            // a App tem de os enviar no Signup ou tem de mudar a BD para NULL.
            
            if (!$profile->save()) {
                throw new \Exception('Erro ao criar perfil: ' . json_encode($profile->getErrors()));
            }

            $transaction->commit();

            return [
                'status' => true,
                'message' => 'Registo efetuado com sucesso.',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'token' => $user->auth_key
                ]
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

}
