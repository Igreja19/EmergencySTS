<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Notificacao;
use yii\web\Response;

class NotificacaoController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['index', 'lida', 'ler-todas', 'stream', 'lista', 'lida-ajax'],
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

    public function actionIndex()
    {
        $user = Yii::$app->user->identity->userprofile ?? null;
        if (!$user) return $this->redirect(['/site/login']);

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

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "notificacao/lida/{$id}",
                    json_encode([
                        'evento' => 'notificacao_lida',
                        'notificacao_id' => $id,
                        'userprofile_id' => $n->userprofile_id,
                        'hora' => date('Y-m-d H:i:s'),
                    ])
                );
            }
        } catch (\Exception $e) {
            Yii::warning("Falha MQTT (Notificacao Lida): " . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    public function actionLerTodas()
    {
        $userId = Yii::$app->user->identity->userprofile->id;

        Notificacao::updateAll(['lida' => 1], [
            'userprofile_id' => $userId,
        ]);

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "notificacao/lidas-todas/{$userId}",
                    json_encode([
                        'evento' => 'todas_notificacoes_lidas',
                        'userprofile_id' => $userId,
                        'hora' => date('Y-m-d H:i:s'),
                    ])
                );
            }
        } catch (\Exception $e) {
            Yii::warning("Falha MQTT (Ler Todas): " . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    public function actionStream()
    {
        $user = Yii::$app->user->identity->userprofile ?? null;
        if (!$user) return;

        $userId = $user->id;

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "notificacao/stream/{$userId}",
                    json_encode([
                        'evento' => 'stream_ativado',
                        'userprofile_id' => $userId,
                        'hora' => date('Y-m-d H:i:s'),
                    ])
                );
            }
        } catch (\Exception $e) {
            // Ignora erro no stream para não quebrar o loop SSE
        }

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

            usleep(500000);
        }
    }

    public function actionLista()
    {
        $this->layout = false;

        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return "Erro: Utilizador sem perfil associado.";
        }

        $userId = Yii::$app->user->identity->userprofile->id;

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "notificacao/lista/{$userId}",
                    json_encode([
                        'evento' => 'notificacao_lista',
                        'userprofile_id' => $userId,
                        'hora' => date('Y-m-d H:i:s'),
                    ])
                );
            }
        } catch (\Exception $e) {
            Yii::warning("Falha MQTT (Lista Widget): " . $e->getMessage());
        }

        $notificacoes = Notificacao::find()
            ->where(['userprofile_id' => $userId])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(20)
            ->all();

        return $this->renderPartial('_lista', [
            'notificacoes' => $notificacoes
        ]);
    }

    public function actionLidaAjax($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $n = Notificacao::findOne($id);

        if (!$n) {
            return ['success' => false, 'error' => 'Notificação não encontrada'];
        }

        if ($n->userprofile_id != Yii::$app->user->identity->userprofile->id) {
            return ['success' => false, 'error' => 'Acesso negado'];
        }

        $n->lida = 1;
        $n->save(false);

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "notificacao/lida-ajax/{$id}",
                    json_encode([
                        'evento' => 'notificacao_lida_ajax',
                        'notificacao_id' => $id,
                        'userprofile_id' => $n->userprofile_id,
                        'hora' => date('Y-m-d H:i:s'),
                    ])
                );
            }
        } catch (\Exception $e) {
            Yii::warning("Falha MQTT (Lida Ajax): " . $e->getMessage());
        }

        return ['success' => true];
    }
}