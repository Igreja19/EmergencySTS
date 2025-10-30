<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // ===== Estatísticas principais =====
        $stats = [
            'espera' => \common\models\Pulseira::find()->where(['status' => 'Aguardando'])->count(),
            'ativas' => \common\models\Pulseira::find()->where(['status' => 'Em atendimento'])->count(),
            'atendidosHoje' => \common\models\Pulseira::find()
                ->where(['status' => 'Atendido'])
                ->andWhere(['>=', 'tempoentrada', date('Y-m-d 00:00:00')])
                ->count(),
            'salasDisponiveis' => 4, // podes ajustar se tiveres tabela de salas
            'salasTotal' => 6,
        ];

        // ===== Contagem por prioridade (Manchester) =====
        $manchester = [
            'vermelho' => \common\models\Pulseira::find()->where(['prioridade' => 'Vermelho'])->count(),
            'laranja'  => \common\models\Pulseira::find()->where(['prioridade' => 'Laranja'])->count(),
            'amarelo'  => \common\models\Pulseira::find()->where(['prioridade' => 'Amarelo'])->count(),
            'verde'    => \common\models\Pulseira::find()->where(['prioridade' => 'Verde'])->count(),
            'azul'     => \common\models\Pulseira::find()->where(['prioridade' => 'Azul'])->count(),
        ];

        // ===== Evolução das triagens (últimos 7 dias) =====
        $evolucaoLabels = [];
        $evolucaoData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = date('Y-m-d', strtotime("-$i days"));
            $evolucaoLabels[] = date('d/m', strtotime($dia));
            $count = \common\models\Triagem::find()
                ->where(['between', 'datatriagem', $dia . ' 00:00:00', $dia . ' 23:59:59'])
                ->count();
            $evolucaoData[] = $count;
        }

        // ===== Pacientes em triagem =====
        $pacientes = \common\models\Triagem::find()
            ->joinWith(['userprofile', 'pulseira'])
            ->orderBy(['datatriagem' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // ===== Últimas triagens =====
        $ultimas = \common\models\Triagem::find()
            ->joinWith(['userprofile', 'pulseira'])
            ->orderBy(['id' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // ===== Notificações =====
        $notificacoes = \common\models\Notificacao::find()
            ->where(['lida' => 0])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // ===== Renderiza a view (envia todas as variáveis) =====
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

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'main-login';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
