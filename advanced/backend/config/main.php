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

    // 🔥 BLOQUEIO CORRETO DO PACIENTE E ROLES INVÁLIDAS
    'on beforeRequest' => function () {

        $route = Yii::$app->requestedRoute;

        // Permitir acesso sem bloqueio
        if (
            $route === 'site/login' ||
            $route === 'site/error' ||
            $route === 'site/acesso-restrito'
        ) {
            return true;
        }

        // Se estiver autenticado
        if (!Yii::$app->user->isGuest) {

            $auth = Yii::$app->authManager;
            $roles = $auth->getRolesByUser(Yii::$app->user->id);

            $rolesValidos = ['admin', 'medico', 'enfermeiro'];
            $temRoleValido = false;

            foreach ($roles as $nome => $roleObj) {
                if (in_array($nome, $rolesValidos)) {
                    $temRoleValido = true;
                    break;
                }
            }

            // ❌ Qualquer role inválida → bloqueado
            if (!$temRoleValido) {
                Yii::$app->response->redirect(['/site/acesso-restrito'])->send();
                return false;
            }
        } else {
            // ❌ Não autenticado → não mostrar login do backend ao paciente
            // Permitir login apenas para staff
            return true;
        }
    },


    'modules' => [
        'api' => [
            'class' => backend\modules\api\ModuleAPI::class,
        ],
    ],

    'components' => [

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

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],

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

                // Paciente
                'GET api/paciente/perfil' => 'api/paciente/perfil',
                'PUT api/paciente/update/<id:\d+>' => 'api/paciente/update',
            ],
        ],
    ],

    'params' => $params,
];
