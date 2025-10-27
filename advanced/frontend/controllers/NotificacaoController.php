<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Notificacao;

class NotificacaoController extends Controller
{
    public function actionIndex()
    {
        // 🔹 Buscar notificações não lidas
        $naoLidas = Notificacao::find()
            ->where(['lida' => 0])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->all();

        // 🔹 Buscar notificações já lidas
        $lidas = Notificacao::find()
            ->where(['lida' => 1])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(50)
            ->all();

        // 🔹 KPIs (contagens)
        $kpiNaoLidas = Notificacao::countNaoLidas();
        $kpiHoje = Notificacao::countHoje();
        $kpiTotal = Notificacao::countTotal();

        return $this->render('index', [
            'naoLidas'   => $naoLidas,
            'lidas'      => $lidas,
            'kpiNaoLidas'=> $kpiNaoLidas,
            'kpiHoje'    => $kpiHoje,
            'kpiTotal'   => $kpiTotal,
        ]);
    }

    // 🔹 Função para marcar todas como lidas
    public function actionMarcarTodasComoLidas()
    {
        Notificacao::updateAll(['lida' => 1]);

        Yii::$app->session->setFlash('success', 'Todas as notificações foram marcadas como lidas.');
        return $this->redirect(['index']);
    }

    public function actionMarcarComoLida($id)
    {
        $notificacao = \common\models\Notificacao::findOne($id);

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
