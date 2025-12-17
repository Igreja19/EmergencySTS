<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\User;          // Importante para editar o email
use common\models\UserProfile;

class EnfermeiroController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $b = parent::behaviors();
        unset($b['authenticator']);

        // Define formato JSON para tudo
        $b['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;

        $b['authenticator'] = [
            'class'      => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $b;
    }

    public function actions()
    {
        $a = parent::actions();
        // Removemos update para criar o nosso personalizado
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // Admin pode tudo
        if (Yii::$app->user->can('admin')) {
            return;
        }

        // Utilizador normal só pode ver/editar o seu próprio perfil
        if ($action === 'view' || $action === 'perfil' || $action === 'update') {
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            throw new ForbiddenHttpException("Não tem permissão para alterar este perfil.");
        }
    }

    // GET /api/enfermeiro/perfil
    public function actionPerfil()
    {
        $userId = Yii::$app->user->id;

        // Procura o perfil pelo ID do utilizador logado
        $perfil = UserProfile::find()
            ->where(['user_id' => $userId])
            ->asArray()
            ->one();

        if (!$perfil) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        $user = User::findOne($userId);
        if ($user) {
            $perfil['email'] = $user->email;
        }

        return $perfil;
    }

    // POST/PUT /api/enfermeiro/{id}
    // O Android envia o userId na URL e os dados no body
    public function actionUpdate($id)
    {
        // 1. Encontrar o perfil pelo user_id (que vem na URL do Android)
        $model = UserProfile::findOne(['user_id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException("Perfil não encontrado para o utilizador $id");
        }

        $this->checkAccess('update', $model);

        // O Android envia dentro de "Enfermeiro", ex: Enfermeiro[nome]
        $dados = Yii::$app->request->post('Enfermeiro');

        if (!$dados) {
            $dados = Yii::$app->request->getBodyParams();
        }

        if (isset($dados['nome']))     $model->nome     = $dados['nome'];
        if (isset($dados['telefone'])) $model->telefone = $dados['telefone'];
        if (isset($dados['nif']))      $model->nif      = $dados['nif'];
        if (isset($dados['sns']))      $model->sns      = $dados['sns'];
        if (isset($dados['morada']))   $model->morada   = $dados['morada'];

        if (isset($dados['datanascimento'])) {
            $model->datanascimento = $dados['datanascimento'];
        }

        if (isset($dados['email'])) {
            $user = User::findOne($model->user_id);
            if ($user) {
                $user->email = $dados['email'];
                $user->save(false); // Save rápido sem validações pesadas
            }
        }

        if ($model->save()) {
            return $model;
        } else {
            Yii::$app->response->statusCode = 422;
            return $model->getErrors();
        }
    }
}