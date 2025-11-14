<?php
namespace backend\modules\api;

use Yii;
use yii\base\Module;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class ModuleAPI extends Module
{
    public $controllerNamespace = 'backend\modules\api\controllers';
    public $defaultRoute = 'default/index';

    public function init()
    {
        parent::init();

        // ----------------------------
        // 🔥 FORÇAR JSON NA API
        // ----------------------------
        Yii::$app->response->on(Response::EVENT_BEFORE_SEND, function ($event) {
            $response = $event->sender;

            if (Yii::$app->controller && Yii::$app->controller->module instanceof self) {
                $response->format = Response::FORMAT_JSON;
            }
        });

        // ----------------------------
        // 🔥 RESTRIÇÃO DE ACESSO → SÓ ADMIN
        // ----------------------------
        Yii::$app->on(\yii\base\Application::EVENT_BEFORE_ACTION, function () {

            // Ativa apenas dentro do módulo API
            if (!(Yii::$app->controller && Yii::$app->controller->module instanceof self)) {
                return true;
            }

            $route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;

            // Permitir login SEM autenticação
            if ($route === 'auth/login') {
                return true;
            }

            // Se não estiver autenticado
            if (Yii::$app->user->isGuest) {
                throw new UnauthorizedHttpException("Precisa de autenticação.");
            }

            // Verificar a role
            $auth = Yii::$app->authManager;
            $roles = $auth->getRolesByUser(Yii::$app->user->id);

            if (!isset($roles['admin'])) {
                throw new UnauthorizedHttpException("Acesso negado: apenas administradores podem usar a API.");
            }

            return true;
        });
    }
}