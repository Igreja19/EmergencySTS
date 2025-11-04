<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Pulseira;
use common\models\UserProfile;

class PulseiraController extends Controller
{
    public function actionIndex()
    {
        // 🔹 Verifica se o utilizador está autenticado
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $userProfileId = Yii::$app->user->identity->userprofile->id ?? null;

        // 🔹 Busca a pulseira do utilizador autenticado (a mais recente)
        $pulseira = Pulseira::find()
            ->where(['userprofile_id' => $userProfileId])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if (!$pulseira) {
            Yii::$app->session->setFlash('warning', 'Nenhuma pulseira associada à sua conta.');
            return $this->render('index', ['pulseira' => null]);
        }

        // 🔹 Nome do utilizador
        $utilizadorNome = Yii::$app->user->identity->userprofile->nome
            ?? Yii::$app->user->identity->userprofile->nomecompleto
            ?? 'Desconhecido';

        // 🔹 Valores base
        $priority = $pulseira->prioridade;
        $agora = time();
        $entradaTs = strtotime($pulseira->tempoentrada ?? date('Y-m-d H:i:s'));
        $tempoDecorridoMin = max(0, floor(($agora - $entradaTs) / 60));

        // 🔹 Configuração por prioridade (Manchester)
        $maxByPriority = [
            'Vermelha' => 0,   // imediato
            'Laranja'  => 10,
            'Amarela'  => 60,
            'Verde'    => 120,
            'Azul'     => 240,
        ];
        $avgServiceMin = [
            'Vermelha' => 10,
            'Laranja'  => 15,
            'Amarela'  => 20,
            'Verde'    => 25,
            'Azul'     => 30,
        ];

        // 🔹 Posição na fila (mesma prioridade)
        $totalAguardarPrioridade = Pulseira::find()
            ->where(['prioridade' => $priority, 'status' => 'Aguardando'])
            ->count();

        // 🔹 Posição do utilizador na fila (1º = à frente)
        $position = Pulseira::find()
                ->where(['prioridade' => $priority, 'status' => 'Aguardando'])
                ->andWhere(['<', 'tempoentrada', $pulseira->tempoentrada])
                ->count() + 1;

        // 🔹 Cálculo do progresso (0% se último, 100% se 1º)
        if ($priority === 'Pendente') {
            $progressPct = 0;
        }
        if ($totalAguardarPrioridade > 1) {
            $progressPct = (($totalAguardarPrioridade - $position) / ($totalAguardarPrioridade - 1)) * 100;
            $progressPct = max(0, min(100, round($progressPct)));
        } else {
            $progressPct = 100;
        }

        // 🔹 Estatísticas gerais
        $totalAguardar = Pulseira::find()->where(['status' => 'Aguardando'])->count();
        $afluencia = $totalAguardar >= 40 ? 'Alta' : ($totalAguardar >= 20 ? 'Moderada' : 'Baixa');

        // 🔹 Fila de pacientes (todos)
        $fila = Pulseira::find()
            ->where(['status' => ['Aguardando', 'Em Atendimento']])
            ->orderBy(['tempoentrada' => SORT_ASC])
            ->limit(15)
            ->all();

        // 🔹 Tempo médio de espera
        $tempoMedio = 0;
        if (!empty($fila)) {
            $totalTempo = 0;
            $count = 0;
            foreach ($fila as $item) {
                if (!empty($item->tempoentrada)) {
                    $totalTempo += floor(($agora - strtotime($item->tempoentrada)) / 60);
                    $count++;
                }
            }
            if ($count > 0) {
                $tempoMedio = round($totalTempo / $count);
            }
        }

        return $this->render('index', [
            'pulseira'          => $pulseira,
            'utilizadorNome'    => $utilizadorNome,
            'tempoDecorridoMin' => $tempoDecorridoMin,
            'position'          => $position,
            'totalAguardar'     => $totalAguardar,
            'afluencia'         => $afluencia,
            'fila'              => $fila,
            'tempoMedio'        => $tempoMedio,
            'maxByPriority'     => $maxByPriority,
            'totalAguardarPrioridade' => $totalAguardarPrioridade,
            'progressPct' => $progressPct,
        ]);
    }

}
