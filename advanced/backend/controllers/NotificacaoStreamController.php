<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Notificacao;

class NotificacaoStreamController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            echo "data: []\n\n";
            flush();
            return;
        }

        $userId = Yii::$app->user->identity->userprofile->id;

        $notificacoes = Notificacao::find()
            ->where(['lida' => 0, 'userprofile_id' => $userId])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        echo "data: " . json_encode($notificacoes) . "\n\n";
        flush();   // <--- OBRIGATÓRIO NO WINDOWS
    }
}
