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

    // NOTA: behaviors() removido porque herda do BaseActiveController

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * LISTAR NOTIFICAÇÕES
     * GET api/notificacao/list
     */
    public function actionList()
    {
        // O BaseActiveController garante que apenas Staff (Médico/Enfermeiro/Admin) acede aqui.

        $user = Yii::$app->user->identity;

        // Validação extra de segurança
        if (!$user || !$user->userprofile) {
            return ['status' => 'error', 'message' => 'Perfil não encontrado'];
        }

        $notificacoes = Notificacao::find()
            ->where(['userprofile_id' => $user->userprofile->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        // MQTT Seguro (Notificação de leitura de lista)
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

    /**
     * MARCAR COMO LIDA
     * POST api/notificacao/ler/{id}
     */
    public function actionLer($id)
    {
        $user = Yii::$app->user->identity;

        if (!$user || !$user->userprofile) {
            return ['status' => 'error', 'message' => 'Token inválido'];
        }

        $notificacao = Notificacao::findOne($id);

        // Garante que a notificação pertence ao utilizador logado
        if (!$notificacao || $notificacao->userprofile_id != $user->userprofile->id) {
            throw new NotFoundHttpException("Notificação não encontrada ou não pertence a este utilizador.");
        }

        $notificacao->lida = 1;
        $notificacao->save(false);

        // MQTT Seguro
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