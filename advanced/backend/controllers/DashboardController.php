<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Pulseira;
use common\models\Triagem;
use common\models\Notificacao;

class DashboardController extends Controller
{
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser(Yii::$app->user->id);

        if (isset($roles['admin'])) {
            return $this->render('admin', $this->getAdminData());
        }

        if (isset($roles['medico'])) {
            return $this->render('medico');
        }

        if (isset($roles['enfermeiro'])) {
            return $this->render('enfermeiro');
        }

        Yii::$app->session->setFlash('error', 'Acesso não permitido.');
        return $this->redirect(['/site/login']);
    }

    public function actionManchester()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $manchester = [
            'vermelho' => Pulseira::find()->where(['prioridade' => 'Vermelho'])->count(),
            'laranja'  => Pulseira::find()->where(['prioridade' => 'Laranja'])->count(),
            'amarelo'  => Pulseira::find()->where(['prioridade' => 'Amarelo'])->count(),
            'verde'    => Pulseira::find()->where(['prioridade' => 'Verde'])->count(),
            'azul'     => Pulseira::find()->where(['prioridade' => 'Azul'])->count(),
        ];

        return $manchester;
    }


    /**
     * Dados do dashboard para ADMIN
     */
    private function getAdminData()
    {
        // Estatísticas
        $stats = [
            'espera' => Pulseira::find()
                ->where(['status' => 'Em espera'])
                ->count(),

            'ativas' => Pulseira::find()
                ->where(['status' => 'Em atendimento'])
                ->count(),

            'atendidosHoje' => Pulseira::find()
                ->where(['status' => 'Atendido'])
                ->andWhere(['between', 'tempoentrada',
                    date('Y-m-d 00:00:00'),
                    date('Y-m-d 23:59:59')
                ])
                ->count(),

            'salasDisponiveis' => 4,
            'salasTotal' => 6,
        ];

        // Prioridades Manchester
        $manchester = [
            'vermelho' => Pulseira::find()->where(['prioridade' => 'Vermelho'])->count(),
            'laranja'  => Pulseira::find()->where(['prioridade' => 'Laranja'])->count(),
            'amarelo'  => Pulseira::find()->where(['prioridade' => 'Amarelo'])->count(),
            'verde'    => Pulseira::find()->where(['prioridade' => 'Verde'])->count(),
            'azul'     => Pulseira::find()->where(['prioridade' => 'Azul'])->count(),
        ];

        /**
         * CORRIGIDO — EVOLUÇÃO DIÁRIA DOS ÚLTIMOS 7 DIAS
         * O gráfico exibe:
         *   - Nº de triagens por dia
         *   - Se não houver triagens → 0
         */
        $evolucaoLabels = [];
        $evolucaoData = [];

        for ($i = 6; $i >= 0; $i--) {

            $dia = date('Y-m-d', strtotime("-$i days"));

            // Label visível no gráfico
            $evolucaoLabels[] = date('d/m', strtotime($dia));

            // Conta triagens apenas desse dia
            $count = Triagem::find()
                ->where(['between', 'datatriagem', "$dia 00:00:00", "$dia 23:59:59"])
                ->count();

            // Se for 0 → mantém 0
            $evolucaoData[] = (int)$count;
        }

        // Últimos pacientes
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

        // Últimas notificações (para o sino)
        $notificacoes = Notificacao::find()
            ->where(['lida' => 0])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        return [
            'stats' => $stats,
            'manchester' => $manchester,
            'evolucaoLabels' => $evolucaoLabels,
            'evolucaoData' => $evolucaoData,
            'pacientes' => $pacientes,
            'ultimas' => $ultimas,
            'notificacoes' => $notificacoes,
        ];
    }
    public function actionStats()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'espera' => Pulseira::find()->where(['status' => 'Em espera'])->count(),
            'ativas' => Pulseira::find()->where(['status' => 'Em atendimento'])->count(),
            'atendidosHoje' => Pulseira::find()
                ->where(['status' => 'Atendido'])
                ->andWhere(['between', 'tempoentrada',
                    date('Y-m-d 00:00:00'),
                    date('Y-m-d 23:59:59')
                ])
                ->count(),
        ];
    }
}
