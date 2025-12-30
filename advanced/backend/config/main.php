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
    
        //  BLOQUEIO DE ACESSO AO BACKEND (INTERFACE WEB)
        
        'on beforeRequest' => function () {
            $route = Yii::$app->requestedRoute ?? '';
    
            // Se a rota começar por 'api/', IGNORA este bloqueio.
            if (strpos($route, 'api/') === 0) {
                return true;
            }
    
            // Permitir acesso livre a páginas de erro/login do backend
            if (in_array($route, ['site/login', 'site/error', 'site/acesso-restrito', 'site/logout'])) {
                return true;
            }
    
            // Se estiver autenticado no Backend (Sessão Web)
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
                // Se for Paciente a tentar entrar no Backend Web -> Bloqueia
                if (!$temRoleValido) {
                    Yii::$app->user->logout();
                    Yii::$app->response->redirect(['/site/acesso-restrito'])->send();
                    return false;
                }
            }
    
            return true;
        },
    
        'modules' => [
            'api' => [
                'class' => backend\modules\api\ModuleAPI::class,
            ],
        ],
    
        'components' => [
    
            'mqtt' => [
                'class' => 'backend\components\MqttService',
                'server' => '127.0.0.1',
                'port' => 1883,
                'clientId' => 'backend-' . rand(1000,9999),
            ],
    
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
    
            //  URL MANAGER DA API
    
            'urlManager' => [
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'rules' => [
                    // Autenticação
                    'POST api/auth/login'  => 'api/auth/login',
                    'GET  api/auth/login'  => 'api/auth/login',
                    'POST api/auth/signup' => 'api/auth/signup',
    
                    // Perfil do user (genérico)
                    'GET api/profile' => 'api/user/index',
    
                    // Atualizar perfil geral
                    'POST api/user/profile/update' => 'api/user/profile/update',
    
                    // Histórico de consultas
                    'GET api/userprofiles/<id:\d+>/consultas' => 'api/consulta/historico',
    
                    // Histórico de triagens
                    'GET api/triagem/historico' => 'api/triagem/historico',
    
                    // Validação de token
                    'GET api/auth/validate' => 'api/auth/validate',
    
                    // Notificações
                    'GET api/notificacao/list'       => 'api/notificacao/list',
                    'POST api/notificacao/ler/<id:\d+>' => 'api/notificacao/ler',
    
                    // Perfil do paciente autenticado
                    'GET api/paciente/perfil' => 'api/paciente/perfil',
    
                    // Perfil do Enfermeiro autenticado
                    'GET api/enfermeiro/perfil' => 'api/enfermeiro/perfil',
    
                    // Diz ao servidor: "Se receberes um POST em api/enfermeiro/{id}, vai para a função actionUpdate"
                    'POST api/enfermeiro/<id:\d+>' => 'api/enfermeiro/update',
    
                    // Diz ao servidor: "Se receberes um POST em api/paciente/{id}, vai para a função actionUpdate"
                    'POST api/paciente/<id:\d+>' => 'api/paciente/update',
                    // Atualizar perfil do paciente (manténs este como estava)
                    'PUT api/paciente/update/<id:\d+>' => 'api/paciente/update',
    
                    [
                        'class' => 'yii\rest\UrlRule',
                        'controller' => [
                            'api/user',
                            'api/triagem',
                            'api/pulseira',
                            'api/consulta',
                            'api/prescricao',
                            'api/notificacao',
                            'api/medicamento',
                            'api/paciente',
                            'api/enfermeiro',
    
                        ],
                        'pluralize' => false,
                        'extraPatterns' => [
                            'GET prioridade' => 'prioridade',
                        ],
                    ],
    
                    // Página base da API
                    'GET api' => 'api/default/index',
                ],
            ],
    
        ],
    
        'params' => $params,
    ];