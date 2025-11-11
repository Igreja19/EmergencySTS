<?php
namespace backend\modules\api;

use Yii;
use yii\base\Module as BaseModule;
use yii\web\Response;

class ModuleAPI extends BaseModule
{
    public $controllerNamespace = 'backend\modules\api\controllers';
    public $defaultRoute = 'default/index';

    public function init()
    {
        parent::init();

        // ✅ Só muda para JSON quando estamos dentro da API
        Yii::$app->on(\yii\base\Application::EVENT_BEFORE_ACTION, function () {
            if (Yii::$app->controller && Yii::$app->controller->module instanceof self) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->formatters = [
                    Response::FORMAT_JSON => [
                        'class' => \yii\web\JsonResponseFormatter::class,
                        'prettyPrint' => YII_DEBUG,
                        'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    ],
                ];
                Yii::$app->controller->layout = false;
            }
        });
    }
}
