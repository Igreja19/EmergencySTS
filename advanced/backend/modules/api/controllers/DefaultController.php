<?php

namespace backend\modules\api\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return [
            "API" => "EmergencySTS API",
            "message" => "Bem-vindo à API EmergencySTS. Utilize um cliente autorizado para aceder aos recursos protegidos."
        ];
    }
}
