<?php

use yii\log\FileTarget;
use yii\web\Response;
use yii\web\JsonResponseFormatter;
use yii\rest\UrlRule;

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

    // 🔥 API MODULE
    'modules' => [
        'api' => [
            'class' => backend\modules\api\ModuleAPI::class,
        ],
    ],

    'components' => [

        // 🔥 FORÇAR JSON NA API — sem quebrar backend
        'response' => [
            'class' => yii\web\Response::class,
        ],


        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],

        'user' => [
            'identityClass' => common\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],

        'session' => [
            'name' => 'advanced-backend',
        ],

        // 🔥 LOG — limpo e funcional
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        // 🔥 AUTH MANAGER (ESSENCIAL PARA O RBAC)
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['@'], // Esta linha é opcional mas boa
        ],
        
        // 🔥 URL MANAGER — 100% corrigido
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

                // REST API
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'api/user',
                        'api/triagem',
                        'api/pulseira',
                        'api/consulta',
                        'api/prescricao',
                        'api/notificacao'
                    ],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET prioridade' => 'prioridade',
                    ],
                ],

                // Autenticação
                'POST api/auth/login'    => 'api/auth/login',
                'GET api/auth/validate'  => 'api/auth/validate',
                'POST api/auth/logout'   => 'api/auth/logout',

                // Notificações
                'GET api/notificacao/list' => 'api/notificacao/list',
                'POST api/notificacao/ler/<id:\d+>' => 'api/notificacao/ler',

                // Página Base
                'GET api' => 'api/default/index',
            ],
        ],
    ],

    'params' => $params,
];