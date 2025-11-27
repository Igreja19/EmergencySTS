<?php

namespace backend\modules\api\controllers;

use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\Notificacao;
use common\models\User;
use Yii;

class NotificacaoController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Resposta em JSON
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Autenticação via auth_key
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    /**
     * 🔹 Lista as notificações do utilizador autenticado
     * GET api/notificacao/list?auth_key=XXXX
     */
    public function actionList()
    {
        $user = Yii::$app->user->identity;

        if (!$user || !$user->userprofile) {
            return ['status' => 'error', 'message' => 'Token inválido'];
        }

        $notificacoes = Notificacao::find()
            ->where(['userprofile_id' => $user->userprofile->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return [
            'status' => 'success',
            'total' => count($notificacoes),
            'data'  => $notificacoes
        ];
    }

    /**
     * 🔹 Marca notificação como lida
     * POST api/notificacao/ler/ID
     */
    public function actionLer($id)
    {
        $user = Yii::$app->user->identity;

        if (!$user || !$user->userprofile) {
            return ['status' => 'error', 'message' => 'Token inválido'];
        }

        $notificacao = Notificacao::findOne($id);

        if (!$notificacao || $notificacao->userprofile_id != $user->userprofile->id) {
            throw new NotFoundHttpException("Notificação não encontrada.");
        }

        $notificacao->lida = 1;
        $notificacao->save(false);

        return [
            'status' => 'success',
            'message' => 'Notificação marcada como lida'
        ];
    }
}
