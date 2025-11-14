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

        // ADMIN → dashboard completo
        if (isset($roles['admin'])) {
            return $this->render('admin', $this->getAdminData());
        }

        // MÉDICO → painel médico
        if (isset($roles['medico'])) {
            return $this->render('medico');
        }

        // ENFERMEIRO → painel enfermeiro
        if (isset($roles['enfermeiro'])) {
            return $this->render('enfermeiro');
        }

        // PACIENTE → acesso negado
        Yii::$app->session->setFlash('error', 'Acesso não permitido.');
        return $this->redirect(['/site/login']);
    }


    /**
     * 🔥 Dados do dashboard para ADMIN
     */
    private function getAdminData()
    {
        // Estatísticas
        $stats = [
            'espera' => Pulseira::find()->where(['status' => 'Em espera'])->count(),
            'ativas' => Pulseira::find()->where(['status' => 'Em atendimento'])->count(),
            'atendidosHoje' => Pulseira::find()
                ->where(['status' => 'Atendido'])
                ->andWhere(['>=', 'tempoentrada', date('Y-m-d 00:00:00')])
                ->count(),
            'salasDisponiveis' => 4,
            'salasTotal' => 6,
        ];

        // Prioridades
        $manchester = [
            'vermelho' => Pulseira::find()->where(['prioridade' => 'Vermelho'])->count(),
            'laranja'  => Pulseira::find()->where(['prioridade' => 'Laranja'])->count(),
            'amarelo'  => Pulseira::find()->where(['prioridade' => 'Amarelo'])->count(),
            'verde'    => Pulseira::find()->where(['prioridade' => 'Verde'])->count(),
            'azul'     => Pulseira::find()->where(['prioridade' => 'Azul'])->count(),
        ];

        // Evolução dos últimos 7 dias
        $evolucaoLabels = [];
        $evolucaoData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = date('Y-m-d', strtotime("-$i days"));
            $evolucaoLabels[] = date('d/m', strtotime($dia));
            $evolucaoData[] = Triagem::find()
                ->where(['between', 'datatriagem', "$dia 00:00:00", "$dia 23:59:59"])
                ->count();
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

        // Últimas notificações
        $notificacoes = Notificacao::find()
            ->where(['lida' => 0])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // retorna o array completo para usar na view admin
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
}
