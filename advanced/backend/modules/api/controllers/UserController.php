<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException; 


class UserController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;
    public $layout = false;

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
        
        unset($actions['index'], $actions['view'], $actions['create']);
        return $actions;
    }

    /**
     * Esta função é o "segurança" para as ações de escrita.
     * @param string 
     * @param \yii\base\Model 
     * @param array 
     * @throws ForbiddenHttpException 
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update' || $action === 'delete') {
            if (!Yii::$app->user->can('admin')) {
                // ...lança um erro 403 (Proibido).
                throw new ForbiddenHttpException("Apenas administradores podem executar esta ação.");
            }
        }
    }

    /**
     * Ação personalizada para 'create' (POST /api/user)
     * Temos de fazer a nossa, porque 'checkAccess' não é chamada para 'create'
     * e o 'actionCreate' padrão não sabe lidar com 'User' e 'UserProfile' ao mesmo tempo.
     */
    public function actionCreate()
    {
        // 1. VERIFICAR PERMISSÃO PRIMEIRO
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem criar utilizadores.");
        }

        // 2. Lógica de criar User + UserProfile
        // (Isto é um exemplo básico, precisa da sua lógica do SignupForm aqui)
        
        $request = Yii::$app->getRequest();
        $params = $request->getBodyParams();

        // 3. Criar o User (tabela 'user')
        $user = new \common\models\User();
        $user->username = $params['username'];
        $user->email = $params['email'];
        $user->setPassword($params['password']); // 'password' vem do JSON
        $user->generateAuthKey();
        $user->status = 10; // Ativo

        if (!$user->save()) {
            Yii::$app->response->statusCode = 422; // Unprocessable Entity
            return ['errors' => $user->getErrors()];
        }

        // 4. Criar o UserProfile (tabela 'userprofile')
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
            // Se o perfil falhar, apaga o user que acabámos de criar (rollback)
            $user->delete();
            Yii::$app->response->statusCode = 422;
            return ['errors' => $profile->getErrors()];
        }
        
        // 5. Atribuir a Role (ex: 'paciente')
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($params['role'] ?? 'paciente'); // Tenta ler a role do JSON, ou 'paciente' por defeito
        if ($role) {
            $auth->assign($role, $user->id);
        }

        Yii::$app->response->statusCode = 201; // 201 Created
        return $profile;
    }


    // ===============================================
    // AS SUAS AÇÕES DE LEITURA (Estão como antes)
    // ===============================================

    /**
     * Lista perfis. (GET /api/user)
     * - Se for Admin, lista TODOS.
     * - Se for Paciente, lista SÓ O SEU.
     */
    public function actionIndex()
    {
        if (Yii::$app->user->can('admin')) {
            $profiles = \common\models\UserProfile::find()->asArray()->all();
            return [
                'Total de perfis' => count($profiles),
                'Data' => $profiles,
            ];
        } else {
            $loggedInUserId = Yii::$app->user->id;
            $profile = \common\models\UserProfile::find()
                ->where(['user_id' => $loggedInUserId])
                ->asArray()
                ->one();
            
            if (!$profile) {
                throw new NotFoundHttpException("Não foi encontrado um perfil para o utilizador logado.");
            }
            return $profile;
        }
    }

    /**
     * Vê um perfil. (GET /api/user/<id>)
     * - Se for Admin, pode ver QUALQUER ID.
     * - Se for Paciente, pode ver SÓ O SEU ID.
     */
    public function actionView($id) 
    {
        $loggedInUserId = Yii::$app->user->id;
        $profile = \common\models\UserProfile::find()
            ->where(['id' => $id]) // 'id' é o ID do perfil
            ->asArray()
            ->one();
            
        if(!$profile) {
            throw new NotFoundHttpException("Perfil com ID {$id} não encontrado.");
        }

        if (!Yii::$app->user->can('admin') && $profile['user_id'] != $loggedInUserId) {
            throw new ForbiddenHttpException("Não tem permissão para ver este perfil.");
        }
        
        return $profile;
    }
}