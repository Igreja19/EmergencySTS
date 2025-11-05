<?php
namespace backend\modules\api\controllers;

use yii\rest\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return [
            'status' => 'ok',
            'message' => 'API ativa 🚀',
            'version' => '1.0',
        ];
    }
}
