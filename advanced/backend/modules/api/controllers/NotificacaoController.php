<?php

namespace backend\modules\api\controllers;

use yii\rest\Controller;
use yii\web\Response;
use common\models\Notificacao;
use common\models\User;

class NotificacaoController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * 🔹 Lista as notificações do utilizador autenticado
     * GET api/notificacao/list?auth_key=XXXX
     */
    public function actionList($auth_key)
    {
        $user = User::findIdentityByAccessToken($auth_key);

        if (!$user) {
            return ['status' => 'error', 'message' => 'Token inválido'];
        }

        $notificacoes = Notificacao::find()
            ->where(['userprofile_id' => $user->userprofile->id])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'status' => 'success',
            'data' => $notificacoes
        ];
    }

    /**
     * 🔹 Marca notificação como lida
     * POST api/notificacao/ler/ID
     */
    public function actionLer($id)
    {
        $n = Notificacao::findOne($id);

        if (!$n) {
            return ['status' => 'error', 'message' => 'Notificação não encontrada'];
        }

        $n->lida = 1;
        $n->save(false);

        return ['status' => 'success', 'message' => 'Notificação marcada como lida'];
    }
}
