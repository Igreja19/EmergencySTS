<?php
namespace frontend\controllers;

use Yii;
use common\models\UserProfile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserProfileController extends Controller
{
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function findModel($id)
    {
        if (($model = UserProfile::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('O perfil solicitado não existe.');
    }
}