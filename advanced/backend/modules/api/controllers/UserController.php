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
        if ($action === 'update') {
            // Se for Admin -> Pode tudo
            if (Yii::$app->user->can('admin')) {
                return;
            }
            // Se for o PRÓPRIO utilizador -> Pode editar
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            // Se não for nem Admin nem o Dono -> Bloqueia
            throw new ForbiddenHttpException("Não tem permissão para editar este perfil.");
        }
        // Se a ação for 'delete'
        if ($action === 'delete') {
            if (!Yii::$app->user->can('admin')) {
                throw new ForbiddenHttpException("Apenas administradores podem apagar utilizadores.");
            }
        }
    }


    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem criar utilizadores.");
        }

        /**
        * Lógica de criar User + UserProfile
        * (Isto é um exemplo básico, precisa da sua lógica do SignupForm aqui)
         * */
        
        $request = Yii::$app->getRequest();
        $params = $request->getBodyParams();

        $user = new \common\models\User();
        $user->username = $params['username'];
        $user->email = $params['email'];
        $user->setPassword($params['password']); 
        $user->generateAuthKey();
        $user->status = 10; 

        if (!$user->save()) {
            Yii::$app->response->statusCode = 422; 
            return ['errors' => $user->getErrors()];
        }

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
        
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($params['role'] ?? 'paciente'); // Tenta ler a role do JSON, ou 'paciente' por defeito
        if ($role) {
            $auth->assign($role, $user->id);
        }

        Yii::$app->response->statusCode = 201; // 201 Created
        return $profile;
    }

    /**
     * Lista perfis. (GET /api/user)
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