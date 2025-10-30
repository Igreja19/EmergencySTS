<?php
namespace backend\controllers;

use yii\web\Controller;
use common\models\Pulseira;
use common\models\Triagem;
use common\models\Notificacao;

class DashboardController extends Controller
{
    public function actionIndex()
    {
        // Estatísticas principais
        $stats = [
            'espera' => Pulseira::find()->where(['status' => 'Aguardando'])->count(),
            'ativas' => Pulseira::find()->where(['status' => 'Em atendimento'])->count(),
            'atendidosHoje' => Pulseira::find()
                ->where(['status' => 'Atendido'])
                ->andWhere(['>=', 'tempoentrada', date('Y-m-d 00:00:00')])
                ->count(),
            'salasDisponiveis' => 4,
            'salasTotal' => 6,
        ];

        // Contagem por prioridade
        $manchester = [
            'vermelho' => Pulseira::find()->where(['prioridade' => 'Vermelho'])->count(),
            'laranja'  => Pulseira::find()->where(['prioridade' => 'Laranja'])->count(),
            'amarelo'  => Pulseira::find()->where(['prioridade' => 'Amarelo'])->count(),
            'verde'    => Pulseira::find()->where(['prioridade' => 'Verde'])->count(),
            'azul'     => Pulseira::find()->where(['prioridade' => 'Azul'])->count(),
        ];

        // Evolução das triagens (últimos 7 dias)
        $evolucaoLabels = [];
        $evolucaoData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = date('Y-m-d', strtotime("-$i days"));
            $evolucaoLabels[] = date('d/m', strtotime($dia));
            $evolucaoData[] = Triagem::find()
                ->where(['between', 'datatriagem', $dia . ' 00:00:00', $dia . ' 23:59:59'])
                ->count();
        }

        // Pacientes e triagens
        $pacientes = Triagem::find()
            ->joinWith(['userprofile', 'pulseira'])
            ->orderBy(['datatriagem' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Últimas triagens
        $ultimas = Triagem::find()
            ->joinWith(['userprofile', 'pulseira'])
            ->orderBy(['id' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // Notificações
        $notificacoes = Notificacao::find()
            ->where(['lida' => 0])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // Envia tudo para a view
        return $this->render('index', [
            'stats' => $stats,
            'manchester' => $manchester,
            'evolucaoLabels' => $evolucaoLabels,
            'evolucaoData' => $evolucaoData,
            'pacientes' => $pacientes,
            'ultimas' => $ultimas,
            'notificacoes' => $notificacoes,
        ]);
    }
}
