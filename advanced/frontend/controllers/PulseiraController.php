<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use common\models\Pulseira;
use common\models\UserProfile;

class PulseiraController extends Controller
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
        // Verifica se o utilizador está autenticado
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $userProfileId = Yii::$app->user->identity->userprofile->id ?? null;

        // BUSCAR PULSEIRA ATIVA
        //  Pertence ao user
        //  Não tem consulta OU a consulta não está encerrada
        //  E MUITO IMPORTANTE: O estado da pulseira não pode ser "Atendido/Finalizado"
        
        $pulseira = Pulseira::find()
            ->joinWith(['triagem t'], false)
            ->joinWith(['triagem.consulta c'], false)
            ->where(['pulseira.userprofile_id' => $userProfileId])
            ->andWhere(['NOT IN', 'pulseira.status', ['Finalizado', 'Atendido', 'Concluido', 'Cancelado']])
            ->andWhere([
                'or',
                ['c.id' => null],                 // ainda nao tem consulta
                ['<>', 'c.estado', 'Encerrada'],  // consulta existe mas nao está encerrada
            ])
            ->orderBy(['pulseira.id' => SORT_DESC])
            ->one();

        // Se não encontrar pulseira (porque a última estava "Atendido"), mostra o ecrã de "Voltar"
        if (!$pulseira) {
            return $this->render('index', ['pulseira' => null]);
        }

        // Nome do utilizador
        $utilizadorNome = Yii::$app->user->identity->userprofile->nome
            ?? Yii::$app->user->identity->userprofile->nomecompleto
            ?? 'Desconhecido';

        // Valores base
        $priority = $pulseira->prioridade;
        $agora = time();
        $entradaTs = strtotime($pulseira->tempoentrada ?? date('Y-m-d H:i:s'));
        $tempoDecorridoMin = max(0, floor(($agora - $entradaTs) / 60));

        // Configuração por prioridade (Manchester)
        $maxByPriority = [
            'Vermelha' => 0,   
            'Laranja'  => 10,
            'Amarela'  => 60,
            'Verde'    => 120,
            'Azul'     => 240,
        ];
        
        // Posição na fila (mesma prioridade)
        $totalAguardarPrioridade = Pulseira::find()
            ->where(['prioridade' => $priority, 'status' => 'Em espera'])
            ->count();

        // Posição do utilizador na fila (1º = à frente)
        $position = Pulseira::find()
                ->where(['prioridade' => $priority, 'status' => 'Em espera'])
                ->andWhere(['<', 'tempoentrada', $pulseira->tempoentrada])
                ->count() + 1;

        // Cálculo do progresso (0% se último, 100% se 1º)
        if ($priority === 'Pendente') {
            $progressPct = 0;
        } else {
            if ($totalAguardarPrioridade > 1) {
                $progressPct = (($totalAguardarPrioridade - $position) / ($totalAguardarPrioridade - 1)) * 100;
                $progressPct = max(0, min(100, round($progressPct)));
            } else {
                $progressPct = 100;
            }
        }

        // Estatísticas gerais
        $totalAguardar = Pulseira::find()->where(['status' => 'Em espera'])->count();
        $afluencia = $totalAguardar >= 40 ? 'Alta' : ($totalAguardar >= 20 ? 'Moderada' : 'Baixa');

        // Fila de pacientes (todos)
        $fila = Pulseira::find()
            ->where(['status' => ['Em espera', 'Em Atendimento']])
            ->orderBy(['tempoentrada' => SORT_ASC])
            ->limit(15)
            ->all();

        // Tempo médio de espera
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