<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use backend\modules\api\controllers\BaseActiveController;
use common\models\Notificacao;

class NotificacaoController extends BaseActiveController
{
    public $modelClass = 'common\models\Notificacao';
    public $enableCsrfValidation = false;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionList()
    {
        $user = Yii::$app->user->identity;

        if (!$user || !$user->userprofile) {
            return ['status' => 'error', 'message' => 'Perfil não encontrado'];
        }

        $notificacoes = Notificacao::find()
            ->where(['userprofile_id' => $user->userprofile->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish(
                    "notificacao/lista/{$user->id}",
                    json_encode([
                        'evento'     => 'notificacoes_listadas',
                        'user_id'    => $user->id,
                        'quantidade' => count($notificacoes),
                        'hora'       => date('Y-m-d H:i:s'),
                    ])
                );
            } catch (\Exception $e) {
                Yii::error("Erro MQTT Notificacao List: " . $e->getMessage());
            }
        }

        return [
            'status' => 'success',
            'total'  => count($notificacoes),
            'data'   => $notificacoes
        ];
    }

    public function actionLer($id)
    {
        $user = Yii::$app->user->identity;

        if (!$user || !$user->userprofile) {
            return ['status' => 'error', 'message' => 'Token inválido'];
        }

        $notificacao = Notificacao::findOne($id);

        if (!$notificacao || $notificacao->userprofile_id != $user->userprofile->id) {
            throw new NotFoundHttpException("Notificação não encontrada ou não pertence a este utilizador.");
        }

        $notificacao->lida = 1;
        $notificacao->save(false);

        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish(
                    "notificacao/lida/{$id}",
                    json_encode([
                        'evento'          => 'notificacao_lida',
                        'notificacao_id'  => $id,
                        'userprofile_id'  => $notificacao->userprofile_id,
                        'hora'            => date('Y-m-d H:i:s'),
                    ])
                );
            } catch (\Exception $e) {
                Yii::error("Erro MQTT Notificacao Ler: " . $e->getMessage());
            }
        }

        return [
            'status'  => 'success',
            'message' => 'Notificação marcada como lida'
        ];
    }
}