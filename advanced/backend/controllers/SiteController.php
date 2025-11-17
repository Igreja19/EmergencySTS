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
                'class' => \yii\filters\AccessControl::class,
                'only' => ['index', 'logout'],
                'rules' => [

                    // 🔐 INDEX → apenas admin, medico e enfermeiro
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['admin', 'medico', 'enfermeiro'],
                    ],

                    // 🔓 LOGOUT → qualquer utilizador autenticado pode sair
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],   // <--- ESTA É A SOLUÇÃO
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
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
            'espera' => \common\models\Pulseira::find()->where(['status' => 'Em espera'])->count(),
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

        // =================================================================
        // 🔍 FILTRO DE DATA PARA GRÁFICO DE EVOLUÇÃO DAS TRIAGENS
        // =================================================================
        $dataFiltro = Yii::$app->request->get('dataFiltro');

        $evolucaoLabels = [];
        $evolucaoData = [];

        if ($dataFiltro) {

            // Apenas 1 dia
            $inicio = $dataFiltro . ' 00:00:00';
            $fim    = $dataFiltro . ' 23:59:59';

            $evolucaoLabels[] = date('d/m/Y', strtotime($dataFiltro));
            $evolucaoData[] = \common\models\Triagem::find()
                ->where(['between', 'datatriagem', $inicio, $fim])
                ->count();

        } else {

            // Últimos 7 dias
            for ($i = 6; $i >= 0; $i--) {
                $dia = date('Y-m-d', strtotime("-$i days"));
                $evolucaoLabels[] = date('d/m', strtotime($dia));

                $count = \common\models\Triagem::find()
                    ->where(['between', 'datatriagem', $dia . ' 00:00:00', $dia . ' 23:59:59'])
                    ->count();

                $evolucaoData[] = $count;
            }
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

        // ===== Renderiza a view =====
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
        if (!Yii::$app->user) {
            return $this->goHome();
        }

        $this->layout = 'main-login';
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            // 🔥 Verificar role antes de permitir login
            $auth = Yii::$app->authManager;
            $roles = $auth->getRolesByUser(Yii::$app->user->id);

            // Apenas admin, médico e enfermeiro podem entrar
            if (!isset($roles['admin']) && !isset($roles['medico']) && !isset($roles['enfermeiro'])) {

                // Terminar sessão imediatamente
                Yii::$app->user->logout();

                // Mostrar página de acesso restrito
                return $this->redirect(['/site/acesso-restrito']);
            }

            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', ['model' => $model]);
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

    public function actionRequestPasswordReset()
    {
        $model = new \common\models\ForgotPasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Verifique o seu email para mais instruções.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Pedimos desculpa, não foi possível enviar o email de recuperação para o endereço fornecido.');
        }

        $this->layout = 'main-login';

        return $this->render('request-password-reset', [
            'model' => $model,
        ]);
    }
    public function actionAcessoRestrito()
    {
        $this->layout = 'main-login'; // 🔥 REMOVE navbar e sidebar

        return $this->render('acesso-restrito');
    }

}
