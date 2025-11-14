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
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfValidation' => false,
        ],

        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],

        'user' => [
            'identityClass' => common\models\User::class,
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
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

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => ['api/user', 'api/triagem', 'api/pulseira'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET prioridade' => 'prioridade',
                    ],
                ],

                'POST api/auth/login' => 'api/auth/login',
                'GET api/auth/validate' => 'api/auth/validate',
                'POST api/auth/logout' => 'api/auth/logout',

                'GET api' => 'api/default/index',
            ],
        ],
    ],


    'params' => $params,
];
