<?php

namespace backend\modules\api\controllers;

use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\Notificacao;
use Yii;

class NotificacaoController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // JSON
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // auth_key
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    /**
     * LISTAR NOTIFICAÇÕES
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

        // MQTT — opcional: avisar que as notificações foram lidas da API
        Yii::$app->mqtt->publish(
            "notificacao/lista/{$user->id}",
            json_encode([
                'evento'     => 'notificacoes_listadas',
                'user_id'    => $user->id,
                'quantidade' => count($notificacoes),
                'hora'       => date('Y-m-d H:i:s'),
            ])
        );

        return [
            'status' => 'success',
            'total'  => count($notificacoes),
            'data'   => $notificacoes
        ];
    }

    /**
     * MARCAR COMO LIDA
     * POST api/notificacao/ler/ID?auth_key=XXXX
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

        // MQTT — notificação lida
        Yii::$app->mqtt->publish(
            "notificacao/lida/{$id}",
            json_encode([
                'evento'          => 'notificacao_lida',
                'notificacao_id'  => $id,
                'userprofile_id'  => $notificacao->userprofile_id,
                'hora'            => date('Y-m-d H:i:s'),
            ])
        );

        return [
            'status'  => 'success',
            'message' => 'Notificação marcada como lida'
        ];
    }
}
