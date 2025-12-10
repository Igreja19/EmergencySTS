<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\User;
use common\models\UserProfile;

class EnfermeiroController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);
        $behaviors['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;

        // autenticação via ?auth_key=
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // admin pode tudo
        if (Yii::$app->user->can('admin')) {
            return;
        }

        // ENFERMEIRO só pode ver o seu próprio perfil
        if ($action === 'view' || $action === 'perfil') {
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            throw new ForbiddenHttpException("Sem permissão para aceder a este perfil.");
        }
    }

    /**
     * GET /api/enfermeiro/perfil
     * Devolve 1 enfermeiro — o que está logado
     */
    public function actionPerfil()
    {
        $userId = Yii::$app->user->id;

        $perfil = UserProfile::find()
            ->where(['user_id' => $userId])
            ->asArray()
            ->one();

        if (!$perfil) {
            throw new NotFoundHttpException("Perfil do enfermeiro não encontrado.");
        }

        return $perfil;
    }

    /**
     * GET /api/enfermeiro/{id}
     */
    public function actionView($id)
    {
        $model = UserProfile::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException("Enfermeiro não encontrado.");
        }

        $this->checkAccess('view', $model);

        return $model;
    }
}
