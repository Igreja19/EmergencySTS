<?php
namespace backend\modules\api\controllers;

use yii\rest\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return [
            'api_name' => 'EmergencySTS API',
            'version' => '1.0',
            'description' => 'API de comunicação entre o sistema EmergencySTS e a aplicação Android.',
            'endpoints' => [
                'path' => '/api/auth/login',
                'method' => 'POST',
                'description' => 'Efetuar login para obter auth_key',
                'params' => ['username', 'password'],
            ],
            [
                'path' => '/api/user?auth_key=XXX',
                'method' => 'GET',
                'description' => 'Lista todos os utilizadores (requer autenticação)',
            ],
            [
                'path' => '/api/triagem/por-prioridade?cor=vermelho&auth_key=XXX',
                'method' => 'GET',
                'description' => 'Lista triagens com uma determinada prioridade (custom action)',
            ],
            [
                'path' => '/api/pulseira/ativas?auth_key=XXX',
                'method' => 'GET',
                'description' => 'Lista pulseiras ainda ativas (custom action)',
            ],
        ];
    }
}
