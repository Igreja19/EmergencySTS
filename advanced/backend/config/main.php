<?php

use Yii; // 👈 IMPORTANTE!

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],

    // 🔹 Criar automaticamente a role paciente + impedir acesso ao backend
    'on beforeRequest' => function () {
        $auth = Yii::$app->authManager;

        // Se por algum motivo não houver authManager (só por segurança)
        if ($auth === null) {
            return;
        }

        // 🔹 1) Criar role paciente se ainda não existir
        if ($auth->getRole('paciente') === null) {
            $role = $auth->createRole('paciente');
            $role->description = 'Paciente do sistema';
            $auth->add($role);
        }

        // 🔹 2) Bloquear pacientes no backend
        if (!Yii::$app->user->isGuest) {
            $userId = Yii::$app->user->id;
            $roles = $auth->getRolesByUser($userId);

            if (isset($roles['paciente'])) {
                // 🔥 logout + mensagem + redirect
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'Acesso exclusivo para staff.');

                Yii::$app->response->redirect(['/site/login'])->send();
                Yii::$app->end();
            }
        }
    },

    // 🔹 Módulo da API
    'modules' => [
        'api' => [
            'class' => backend\modules\api\ModuleAPI::class,
        ],
    ],

    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => common\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        // 🔹 URL Manager da API
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

                // 🔹 Controladores REST automáticos
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => ['api/user', 'api/triagem', 'api/pulseira'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET prioridade' => 'prioridade',
                    ],
                ],

                // 🔹 Endpoints manuais (Auth)
                'POST api/auth/login'    => 'api/auth/login',
                'GET api/auth/validate'  => 'api/auth/validate',
                'POST api/auth/logout'   => 'api/auth/logout',

                // 🔹 Página base da API
                'GET api' => 'api/default/index',
            ],
        ],
    ],

    'params' => $params,
];
