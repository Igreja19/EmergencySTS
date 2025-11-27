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
            'optional' => ['login', 'signup', 'validate'], 
        ];
        return $behaviors;
    }


    //  LOGIN (POST /api/auth/login)
   
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
        $role = Yii::$app->db->createCommand("SELECT item_name FROM auth_assignment WHERE user_id = :user_id LIMIT 1")
            ->bindValue(':user_id', $user->id)
            ->queryScalar();

        // Buscar o Perfil (Para a App saber o userprofile_id)
        $profile = UserProfile::findOne(['user_id' => $user->id]);

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


    //  REGISTO (POST /api/auth/signup)
   
    public function actionSignup()
    {
        $data = Yii::$app->request->post();

        // Validação básica
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

            // Atribuir Role 'paciente'
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('paciente');
            if ($authorRole) {
                $auth->assign($authorRole, $user->id);
            }

            // Criar Perfil
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->nome = $data['nome'] ?? $user->username;
            $profile->email = $user->email;
            // Campos opcionais (a App pode enviar depois no Editar Perfil)
            $profile->nif = $data['nif'] ?? null;
            $profile->sns = $data['sns'] ?? null;
            $profile->telefone = $data['telefone'] ?? null;
            $profile->genero = $data['genero'] ?? null;
            $profile->datanascimento = $data['datanascimento'] ?? null;

            if (!$profile->save()) {
                throw new \Exception("Erro no perfil: " . json_encode($profile->errors));
            }

            $transaction->commit();

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

    //  VALIDAR TOKEN (GET /api/auth/validate)

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
                'email' => $user->email,
            ],
        ];
    }
}