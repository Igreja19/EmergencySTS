<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Controlador Base para a API.
 * Todos os controladores que herdarem disto estarão protegidos contra Pacientes.
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
        $user = Yii::$app->user;

        // Se for admin, médico ou enfermeiro -> DEIXA PASSAR
        if ($user->can('admin') || $user->can('medico') || $user->can('enfermeiro')) {
            return true;
        }

        // Se for apenas paciente (ou user sem permissões) -> BLOQUEIA
        throw new ForbiddenHttpException("Apenas profissionais de saúde têm acesso à API Mobile.");
    }
}