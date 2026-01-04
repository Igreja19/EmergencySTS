<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Controlador Base para a API.
 * Todos os controladores que herdarem disto herdam a autenticação e o formato JSON.
 */
class BaseActiveController extends ActiveController
{
    // Configuração Global de Formatação JSON e Autenticação
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        // Forçar resposta em JSON
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Autenticação por token na URL (?auth_key=...)
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    /**
     * O GUARDA-COSTAS DA API
     * Este método corre ANTES de qualquer ação.
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        
        // Correção do erro "Undefined variable $user"
        $user = Yii::$app->user;

        // Apenas garante que está logado. 
        // A distinção entre Paciente vs Médico será feita no checkAccess() de cada controller.
        if ($user->isGuest) {
            throw new ForbiddenHttpException("Tem de realizar login.");
        }

        return true;
    }
}