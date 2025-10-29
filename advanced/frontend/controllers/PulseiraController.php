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
        // 🔹 Última pulseira criada
        $pulseira = Pulseira::find()->orderBy(['id' => SORT_DESC])->one();

        if (!$pulseira) {
            Yii::$app->session->setFlash('warning', 'Nenhuma pulseira encontrada.');
            return $this->render('index', ['pulseira' => null]);
        }

        // 🔹 Nome do utilizador (ligado ao perfil)
        $utilizadorNome = 'Desconhecido';
        if ($pulseira->userprofile_id) {
            $userProfile = UserProfile::findOne($pulseira->userprofile_id);
            if ($userProfile) {
                $utilizadorNome = $userProfile->nome ?? $userProfile->nomecompleto ?? 'Utilizador';
            }
        }


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

        // 🔹 Corrige se não houver campo status na tabela
        $hasStatus = Yii::$app->db->createCommand("
            SHOW COLUMNS FROM pulseira LIKE 'status'
        ")->queryOne();

        if (!$hasStatus) {
            Yii::$app->db->createCommand("ALTER TABLE pulseira ADD COLUMN status ENUM('Aguardando','Em Atendimento','Concluído') DEFAULT 'Aguardando';")->execute();
        }

        // 🔹 Posição na fila (mesma prioridade)
        $position = Pulseira::find()
                ->where(['prioridade' => $priority, 'status' => 'Aguardando'])
                ->andWhere(['<', 'tempoentrada', $pulseira->tempoentrada])
                ->count() + 1;

        // 🔹 Estimativa de tempo
        $tempoEstimadoMin = 0;
        if (isset($avgServiceMin[$priority])) {
            $tempoEstimadoMin = max(0, ($position - 1) * $avgServiceMin[$priority] - $tempoDecorridoMin);
        }

        // 🔹 Progresso até ao tempo máximo recomendado
        $maxMin = $maxByPriority[$priority] ?? 60;
        $progressPct = $maxMin > 0 ? min(100, round(($tempoDecorridoMin / $maxMin) * 100)) : 100;

        // 🔹 Estatísticas gerais
        $totalAguardar = Pulseira::find()->where(['status' => 'Aguardando'])->count();
        $afluencia = $totalAguardar >= 40 ? 'Alta' : ($totalAguardar >= 20 ? 'Moderada' : 'Baixa');

        // 🔹 Fila de pacientes da mesma prioridade
        $fila = Pulseira::find()
            ->where(['status' => 'Aguardando'])
            ->orderBy(['tempoentrada' => SORT_ASC])
            ->limit(10)
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
            'tempoEstimadoMin'  => $tempoEstimadoMin,
            'position'          => $position,
            'progressPct'       => $progressPct,
            'totalAguardar'     => $totalAguardar,
            'afluencia'         => $afluencia,
            'fila'              => $fila,
            'tempoMedio'        => $tempoMedio ?? 0,
        ]);

    }
}
