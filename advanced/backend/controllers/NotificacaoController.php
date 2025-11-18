<?php

namespace backend\controllers;

use Yii;
use common\models\Notificacao;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class NotificacaoController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'lida'      => ['POST', 'GET'],
                    'ler-todas' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return $this->redirect(['site/login']);
        }

        $userId = Yii::$app->user->identity->userprofile->id;

        return $this->render('index', [
            'naoLidas' => Notificacao::find()->where([
                'userprofile_id' => $userId, 'lida' => 0
            ])->orderBy(['dataenvio' => SORT_DESC])->all(),

            'todas' => Notificacao::find()->where([
                'userprofile_id' => $userId
            ])->orderBy(['dataenvio' => SORT_DESC])->all(),
        ]);
    }

    public function actionLida($id)
    {
        $n = Notificacao::findOne($id);
        if (!$n) throw new NotFoundHttpException("Notificação não encontrada.");
        if ($n->userprofile_id != Yii::$app->user->identity->userprofile->id)
            throw new NotFoundHttpException("Acesso negado.");

        $n->lida = 1;
        $n->save(false);

        return $this->redirect(['index']);
    }

    public function actionLerTodas()
    {
        $userId = Yii::$app->user->identity->userprofile->id;

        Notificacao::updateAll(['lida' => 1], [
            'userprofile_id' => $userId,
            'lida' => 0
        ]);

        return $this->redirect(['index']);
    }

    public function actionStream()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return;
        }

        $userId = Yii::$app->user->identity->userprofile->id;

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        while (true) {

            $notificacoes = Notificacao::find()
                ->where(['userprofile_id' => $userId, 'lida' => 0])
                ->orderBy(['dataenvio' => SORT_DESC])
                ->limit(5)
                ->asArray()
                ->all();

            echo "data: " . json_encode($notificacoes) . "\n\n";

            ob_flush();
            flush();

            usleep(500000); // 0.5 segundos
        }
    }
}
