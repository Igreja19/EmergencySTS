<?php
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

    // 🔹 módulo API
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
                // 🔹 Controladores REST (automáticos)
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => ['api/user', 'api/triagem', 'api/pulseira'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET prioridade' => 'prioridade',
                    ],
                ],

                // 🔹 Endpoints manuais (Auth)
                'POST api/auth/login' => 'api/auth/login',
                'GET api/auth/validate' => 'api/auth/validate',
                'POST api/auth/logout' => 'api/auth/logout',

                // 🔹 Rota base da API
                'GET api' => 'api/default/index',
            ],
        ],

    ],

    'params' => $params,
];
