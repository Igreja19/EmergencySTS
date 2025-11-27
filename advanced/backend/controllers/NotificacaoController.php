<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Notificacao;

class NotificacaoController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                // 🔐 Acesso apenas para utilizadores autenticados com roles válidos
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'only' => ['index', 'lida', 'ler-todas', 'stream'],
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['admin', 'medico', 'enfermeiro'],
                        ],
                    ],
                    'denyCallback' => function () {
                        return Yii::$app->response->redirect(['/site/login']);
                    },
                ],

                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'lida' => ['POST', 'GET'],
                        'ler-todas' => ['POST', 'GET'],
                    ],
                ],
            ]
        );
    }

    /**
     * 📌 LISTAGEM DE TODAS AS NOTIFICAÇÕES
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity->userprofile ?? null;
        if (!$user) {
            return $this->redirect(['/site/login']);
        }

        $userId = $user->id;

        return $this->render('index', [
            'naoLidas' => Notificacao::find()
                ->where(['userprofile_id' => $userId, 'lida' => 0])
                ->orderBy(['dataenvio' => SORT_DESC])
                ->all(),

            'todas' => Notificacao::find()
                ->where(['userprofile_id' => $userId])
                ->orderBy(['dataenvio' => SORT_DESC])
                ->all(),
        ]);
    }

    /**
     * 📌 MARCAR UMA NOTIFICAÇÃO COMO LIDA
     */
    public function actionLida($id)
    {
        $n = Notificacao::findOne($id);
        if (!$n) {
            throw new NotFoundHttpException("Notificação não encontrada.");
        }

        if ($n->userprofile_id != Yii::$app->user->identity->userprofile->id) {
            throw new NotFoundHttpException("Acesso negado.");
        }

        $n->lida = 1;
        $n->save(false);

        return $this->redirect(['index']);
    }

    /**
     * 📌 MARCAR TODAS COMO LIDAS
     */
    public function actionLerTodas()
    {
        $userId = Yii::$app->user->identity->userprofile->id;

        Notificacao::updateAll(['lida' => 1], [
            'userprofile_id' => $userId,
        ]);

        return $this->redirect(['index']);
    }

    /**
     * 📡 SSE — STREAM DE NOTIFICAÇÕES EM TEMPO REAL
     */
    public function actionStream()
    {
        $user = Yii::$app->user->identity->userprofile ?? null;
        if (!$user) return;

        $userId = $user->id;

        // Headers obrigatórios SSE:
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        while (true) {

            $notificacoes = Notificacao::find()
                ->where(['userprofile_id' => $userId, 'lida' => 0])
                ->orderBy(['dataenvio' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();

            echo "data: " . json_encode($notificacoes) . "\n\n";

            ob_flush();
            flush();

            usleep(500000); // 0.5 segundos
        }
    }
}
