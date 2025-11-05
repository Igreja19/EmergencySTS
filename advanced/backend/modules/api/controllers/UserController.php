<?php
namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use Yii;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;
    public $layout = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            throw new UnauthorizedHttpException('Acesso negado. Faça login primeiro.');
        }

        return parent::beforeAction($action);
    }

    // ✅ Sobrescreve a listagem para formatar o JSON
    public function actions()
    {
        $actions = parent::actions();

        // Remove o index original para personalizar
        unset($actions['index'], $actions['view']);

        return $actions;
    }

    public function actionIndex()
    {
        $users = \common\models\UserProfile::find()->asArray()->all();

        return [
            'Total de utilizadores' => count($users),
            'Data' => $users,
        ];
    }
    public function actionView($id) {
        $user = \common\models\UserProfile::find()->asArray()->where(['id' => $id])->one();
        if(!$user) {
            throw new \yii\web\NotFoundHttpException("Utilizador com ID {$id} não encontrado.");
        }
        return [
            'Data' => $user,
        ];
    }
}
