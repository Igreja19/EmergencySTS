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
        $user = Yii::$app->user->identity;
        $userProfileId = $user->userprofile->id ?? null;
        $agora = time();

        $pulseira = Pulseira::find()
            ->joinWith(['triagem t'], false)
            ->joinWith(['triagem.consulta c'], false)
            ->where(['pulseira.userprofile_id' => $userProfileId])
            ->andWhere(['NOT IN', 'pulseira.status', ['Finalizado', 'Atendido', 'Concluido', 'Cancelado']])
            ->andWhere([
                'or',
                ['c.id' => null],
                ['<>', 'c.estado', 'Encerrada'],
            ])
            ->orderBy(['pulseira.id' => SORT_DESC])
            ->one();

        if (!$pulseira) {
            return $this->render('index', ['pulseira' => null]);
        }

        $utilizadorNome = $user->userprofile->nome
            ?? $user->userprofile->nomecompleto
            ?? 'Utilizador';

        $priority = $pulseira->prioridade;
        $entradaTs = strtotime($pulseira->tempoentrada);
        $tempoDecorridoMin = ($entradaTs) ? max(0, floor(($agora - $entradaTs) / 60)) : 0;

        $maxByPriority = [
            'Vermelha' => 0,
            'Laranja'  => 10,
            'Amarela'  => 60,
            'Verde'    => 120,
            'Azul'     => 240,
        ];

        $totalAguardarPrioridade = Pulseira::find()
            ->where(['prioridade' => $priority, 'status' => 'Em espera'])
            ->count();

        $position = Pulseira::find()
                ->where(['prioridade' => $priority, 'status' => 'Em espera'])
                ->andWhere(['<', 'tempoentrada', $pulseira->tempoentrada])
                ->count() + 1;

        $progressPct = 0;
        if ($priority !== 'Pendente') {
            $tempoMaximoDesejado = $maxByPriority[$priority] ?? 60;

            if ($tempoMaximoDesejado > 0) {
                $progressPct = ($tempoDecorridoMin / $tempoMaximoDesejado) * 100;

                $progressPct = max(5, min(100, round($progressPct)));
            } else {
                $progressPct = 100;
            }
        }

        $totalAguardar = Pulseira::find()->where(['status' => 'Em espera'])->count();
        $afluencia = ($totalAguardar >= 40) ? 'Alta' : (($totalAguardar >= 20) ? 'Moderada' : 'Baixa');

        $fila = Pulseira::find()
            ->where(['status' => ['Em espera', 'Em Atendimento']])
            ->orderBy(['tempoentrada' => SORT_ASC])
            ->limit(15)
            ->all();

        $tempoMedio = 0;
        if (!empty($fila)) {
            $totalSegundos = 0;
            $count = 0;
            foreach ($fila as $item) {
                $ts = strtotime($item->tempoentrada);
                if ($ts && $ts <= $agora) {
                    $totalSegundos += ($agora - $ts);
                    $count++;
                }
            }
            if ($count > 0) {
                $tempoMedio = round(($totalSegundos / $count) / 60);
            }
        }

        return $this->render('index', [
            'pulseira'                => $pulseira,
            'utilizadorNome'          => $utilizadorNome,
            'tempoDecorridoMin'       => (int)$tempoDecorridoMin,
            'position'                => $position,
            'totalAguardar'           => $totalAguardar,
            'afluencia'               => $afluencia,
            'fila'                    => $fila,
            'tempoMedio'              => $tempoMedio,
            'maxByPriority'           => $maxByPriority,
            'totalAguardarPrioridade' => $totalAguardarPrioridade,
            'progressPct'             => $progressPct,
        ]);
    }
}