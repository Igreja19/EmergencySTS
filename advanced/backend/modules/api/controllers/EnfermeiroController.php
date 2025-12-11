<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

use common\models\UserProfile;

class EnfermeiroController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $b = parent::behaviors();
        unset($b['authenticator']);

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
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (Yii::$app->user->can('admin')) {
            return;
        }

        if ($action === 'view' || $action === 'perfil') {
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            throw new ForbiddenHttpException("Sem permissão para aceder a este perfil.");
        }
    }

    // GET /api/enfermeiro/perfil
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

    // GET /api/enfermeiro/{id}
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
