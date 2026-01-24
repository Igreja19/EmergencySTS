<?php

namespace backend\controllers;

use common\helpers\IpHelper;
use common\models\Consulta;
use common\models\ForgotPasswordForm;
use common\models\LoginForm;
use common\models\LoginHistory;
use common\models\Notificacao;
use common\models\Pulseira;
use common\models\Triagem;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
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
                'only' => ['index', 'logout'],
                'rules' => [

                    // INDEX → apenas admin, medico e enfermeiro
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['admin', 'medico', 'enfermeiro'],
                    ],

                    // LOGOUT → qualquer utilizador autenticado pode sair
                    [
                        'actions' => ['logout'],
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
                'class' => ErrorAction::class,
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
        $user = Yii::$app->user->identity;
        $isAdmin = Yii::$app->authManager->checkAccess($user->id, 'admin');
        $isEnfermeiro = Yii::$app->authManager->checkAccess($user->id, 'enfermeiro');
        $isMedico = Yii::$app->authManager->checkAccess($user->id, 'medico');

        $stats = [
            'espera' => Pulseira::find()->where(['status' => 'Em espera'])->count(),
            'ativas' => Pulseira::find()->where(['status' => 'Em atendimento'])->count(),
            'atendidosHoje' => Consulta::find()
                ->where(['estado' => Consulta::ESTADO_ENCERRADA])
                ->andWhere(['between', 'data_encerramento', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
                ->count(),
            'triagensPendentes' => Pulseira::find()
                ->where(['prioridade' => 'Pendente'])
                ->count(),
            'totalUtilizadores' => User::find()->count(),
            'salasDisponiveis' => 4,
            'salasTotal' => 6,
        ];

        $manchester = [
            'vermelho' => Pulseira::find()->where(['prioridade' => 'Vermelho'])->count(),
            'laranja'  => Pulseira::find()->where(['prioridade' => 'Laranja'])->count(),
            'amarelo'  => Pulseira::find()->where(['prioridade' => 'Amarelo'])->count(),
            'verde'    => Pulseira::find()->where(['prioridade' => 'Verde'])->count(),
            'azul'     => Pulseira::find()->where(['prioridade' => 'Azul'])->count(),
        ];

        $dataFiltro = Yii::$app->request->get('dataFiltro');

        $evolucaoLabels = [];
        $evolucaoData = [];

        if ($dataFiltro) {
            $inicio = $dataFiltro . ' 00:00:00';
            $fim    = $dataFiltro . ' 23:59:59';

            $evolucaoLabels[] = date('d/m/Y', strtotime($dataFiltro));
            $evolucaoData[] = Triagem::find()
                ->where(['between', 'datatriagem', $inicio, $fim])
                ->count();
        } else {
            // Últimos 7 dias por defeito
            for ($i = 6; $i >= 0; $i--) {
                $dia = date('Y-m-d', strtotime("-$i days"));
                $evolucaoLabels[] = date('d/m', strtotime($dia));

                $count = Triagem::find()
                    ->where(['between', 'datatriagem', $dia . ' 00:00:00', $dia . ' 23:59:59'])
                    ->count();

                $evolucaoData[] = (int)$count;
            }
        }

        $pacientes = Triagem::find()
            ->joinWith([
                'userprofile.user',
                'pulseira'
            ])
            ->where(['in', 'pulseira.status', ['Em espera', 'Em atendimento']])
            ->andWhere([
                'user.status' => User::STATUS_ACTIVE,
                'userprofile.estado' => 1,
            ])
            ->orderBy(['datatriagem' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        $ultimas = Triagem::find()
            ->joinWith(['userprofile', 'pulseira'])
            ->where(['<>', 'pulseira.prioridade', 'Pendente'])
            ->orderBy(['id' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        $notificacoes = [];
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->userprofile) {
            $userprofileId = Yii::$app->user->identity->userprofile->id;
            $notificacoes = Notificacao::find()
                ->where([
                    'lida' => 0,
                    'userprofile_id' => $userprofileId,
                ])
                ->orderBy(['dataenvio' => SORT_DESC])
                ->limit(5)
                ->asArray()
                ->all();
        }

        $logins = [];
        if ($isAdmin) {
            $logins = LoginHistory::find()
                ->joinWith('user')
                ->orderBy(['data_login' => SORT_DESC])
                ->limit(20)
                ->asArray()
                ->all();
        }

        return $this->render('index', [
            'stats'          => $stats,
            'manchester'     => $manchester,
            'evolucaoLabels' => $evolucaoLabels,
            'evolucaoData'   => $evolucaoData,
            'pacientes'      => $pacientes,
            'ultimas'        => $ultimas,
            'notificacoes'   => $notificacoes,
            'isAdmin'        => $isAdmin,
            'isEnfermeiro'   => $isEnfermeiro,
            'isMedico'       => $isMedico,
            'logins'         => $logins,
        ]);
    }

    public function actionGraficoDados()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $manchester = $this->getManchesterStats();  // Ajusta para tua lógica real
        $evolucaoLabels = $this->getEvolucaoLabels();
        $evolucaoData = $this->getEvolucaoData();

        return [
            'manchester' => [
                'vermelho' => (int)$manchester['vermelho'],
                'laranja'  => (int)$manchester['laranja'],
                'amarelo'  => (int)$manchester['amarelo'],
                'verde'    => (int)$manchester['verde'],
                'azul'     => (int)$manchester['azul'],
            ],
            'evolucaoLabels' => $evolucaoLabels,
            'evolucaoData'   => array_map('intval', $evolucaoData),
        ];
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        $this->actionDestroyCookie();

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'main-login';
        $model = new LoginForm();
        $model->scenario = LoginForm::SCENARIO_BACKEND;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            $userId = Yii::$app->user->id;

            // Histórico de login
            $history = new LoginHistory();
            $history->user_id = $userId;
            $history->ip = Yii::$app->request->userIP;
            $history->user_agent = Yii::$app->request->userAgent;
            $history->save(false);

            return $this->goBack();
        }

        // Credenciais válidas mas sem permissões
        if ($model->acessoRestrito) {
            return $this->redirect(['/site/acesso-restrito']);
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

    public function actionRequestPasswordReset()
    {
        $model = new ForgotPasswordForm();

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
        $this->layout = 'main-login';

        $this->actionDestroyCookie();

        return $this->render('acesso-restrito');
    }

    private function actionDestroyCookie()
    {
        $cookies = Yii::$app->response->cookies;

        // backend identity cookie
        $cookies->remove('advanced-backend');

        Yii::$app->user->logout(false);
        Yii::$app->session->destroy();
    }

}
