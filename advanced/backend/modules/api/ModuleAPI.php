<?php
namespace backend\modules\api;

use Yii;
use yii\base\Module;
use yii\web\Response;

class ModuleAPI extends Module
{
    public $controllerNamespace = 'backend\modules\api\controllers';
    public $defaultRoute = 'default/index';

    public function init()
    {
        parent::init();

        Yii::$app->errorHandler->errorAction = null;

        Yii::$app->user->loginUrl = null;
        Yii::$app->user->enableSession = false;

        // 3. Força JSON
        Yii::$app->response->on(Response::EVENT_BEFORE_SEND, function ($event) {
            $response = $event->sender;
            if (Yii::$app->controller && Yii::$app->controller->module instanceof self) {
                $response->format = Response::FORMAT_JSON;
            }
        });
    }
}