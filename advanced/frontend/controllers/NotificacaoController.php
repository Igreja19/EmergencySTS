<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use common\models\Notificacao;

class NotificacaoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['paciente'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;

        if (!$user || !$user->userprofile) {
            return $this->redirect(['site/login']);
        }

        $queryBase = Notificacao::find()
            ->where(['userprofile_id' => $user->userprofile->id])
            ->andWhere([
                'or',

                ['like', 'mensagem', 'Consulta Encerrada'],
                ['like', 'titulo',   'Consulta Encerrada'],

                ['like', 'mensagem', 'Consulta iniciada'],
                ['like', 'titulo',   'Consulta iniciada'],

                ['like', 'mensagem', 'Pulseira atribu'],
                ['like', 'titulo',   'Pulseira atribu'],
            ]);

        $naoLidasQuery = clone $queryBase;
        $naoLidas = $naoLidasQuery
            ->andWhere(['lida' => 0])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->all();

        $lidasQuery = clone $queryBase;
        $lidas = $lidasQuery
            ->andWhere(['lida' => 1])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(50)
            ->all();

        $kpiNaoLidas = count($naoLidas);
        $kpiTotal = $queryBase->count();

        $kpiHojeQuery = clone $queryBase;
        $kpiHoje = $kpiHojeQuery
            ->andWhere(['>=', 'dataenvio', date('Y-m-d 00:00:00')])
            ->andWhere(['<=', 'dataenvio', date('Y-m-d 23:59:59')])
            ->count();

        return $this->render('index', [
            'naoLidas'    => $naoLidas,
            'lidas'       => $lidas,
            'kpiNaoLidas' => $kpiNaoLidas,
            'kpiHoje'     => $kpiHoje,
            'kpiTotal'    => $kpiTotal,
        ]);
    }

    public function actionMarcarTodasComoLidas()
    {
        Notificacao::updateAll(['lida' => 1]);

        Yii::$app->session->setFlash('success', 'Todas as notificações foram marcadas como lidas.');
        return $this->redirect(['index']);
    }

    public function actionMarcarComoLida($id)
    {
        $notificacao = Notificacao::findOne($id);

        if ($notificacao) {
            $notificacao->lida = 1;
            $notificacao->save(false);
            Yii::$app->session->setFlash('success', 'Notificação marcada como lida.');
        } else {
            Yii::$app->session->setFlash('error', 'Notificação não encontrada.');
        }

        return $this->redirect(['index']);
    }
}
