<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\Triagem;
use common\models\TriagemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class TriagemController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [

                // 🔒 CONTROLO DE ACESSO (protege rotas)
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'only' => ['index','view','create','update','delete','chart-data'], // rotas protegidas
                    'rules' => [

                        // 👉 login e error apenas no SiteController (ignora aqui)
                        [
                            'allow' => true,
                            'actions' => ['error', 'login'],
                        ],

                        // 👉 permitir apenas ADMIN, MÉDICO e ENFERMEIRO
                        [
                            'allow' => true,
                            'roles' => ['admin', 'medico', 'enfermeiro'],
                        ],
                    ],
                    'denyCallback' => function () {
                        return Yii::$app->response->redirect(['/site/login']);
                    },
                ],

                // 🔧 VerbFilter já existia, continua igual
                'verbs' => [
                    'class' => \yii\filters\VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'chart-data' => ['GET'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lista Triagens
     */
    public function actionIndex()
    {
        $searchModel = new TriagemSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Ver Triagem
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Criar Triagem + 🔔 Notificações Automáticas
     */
    public function actionCreate()
    {
        $model = new Triagem();

        // 🔥 SE FOR POST, já existe userprofile → vamos validar
        if ($this->request->isPost) {

            // Primeiro carregamos os dados enviados
            if ($model->load($this->request->post())) {

                // =====================================================
                // ❌  VERIFICAR SE O UTILIZADOR JÁ TEM PULSEIRA
                // =====================================================
                $pulseiraExistente = \common\models\Pulseira::find()
                    ->where(['userprofile_id' => $model->userprofile_id])
                    ->andWhere(['IS NOT', 'prioridade', null])   // só pulseiras atribuídas
                    ->one();

                if ($pulseiraExistente) {
                    Yii::$app->session->setFlash(
                        'danger',
                        "Este paciente já tem pulseira atribuída. Não pode criar nova triagem."
                    );

                    return $this->redirect(['index']);
                }

                // =====================================================
                // ❌  VERIFICAR SE UTILIZADOR TEM UMA TRIAGEM PENDENTE
                // =====================================================
                $triagemExistente = \common\models\Triagem::find()
                    ->where(['userprofile_id' => $model->userprofile_id])
                    ->andWhere(['pulseira_id' => null]) // Triagem ainda sem pulseira atribuída
                    ->one();

                if ($triagemExistente) {
                    Yii::$app->session->setFlash(
                        'danger',
                        "Este paciente já tem uma triagem pendente. Deve atribuir uma pulseira antes de criar nova triagem."
                    );

                    return $this->redirect(['index']);
                }

                // =====================================================
                // 🔥  SE PASSOU NAS VALIDAÇÕES → GUARDAR
                // =====================================================
                if ($model->save()) {

                    // =====================================================
                    // 🔔 NOTIFICAÇÕES AUTOMÁTICAS
                    // =====================================================
                    $userId = $model->userprofile_id;

                    // 1️⃣ Notificação geral
                    Notificacao::enviar(
                        $userId,
                        "Triagem registada",
                        "Foi registada uma nova triagem para o paciente " . $model->userprofile->nome . ".",
                        "Consulta"
                    );

                    // 2️⃣ Notificação crítica
                    if ($model->pulseira && in_array($model->pulseira->prioridade, ["Vermelho", "Laranja"])) {
                        Notificacao::enviar(
                            $userId,
                            "Prioridade " . $model->pulseira->prioridade,
                            "O paciente " . $model->userprofile->nome . " encontra-se em prioridade " . $model->pulseira->prioridade . ".",
                            "Prioridade"
                        );
                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        // ⏳ Default
        $model->loadDefaultValues();

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Atualizar Triagem
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Apagar Triagem
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Procurar Triagem
     */
    protected function findModel($id)
    {
        if (($model = Triagem::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * =====================================================
     * 🔍 API Ajax — Dados para o gráfico de evolução
     * =====================================================
     *
     * /triagem/chart-data?start=2025-02-05&end=2025-02-10
     */
    public function actionChartData($start = null, $end = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = Triagem::find();

        // Filtrar intervalo de datas
        if ($start && $end) {
            $query->andWhere(['between', 'datatriagem', $start . ' 00:00:00', $end . ' 23:59:59']);
        }

        $triagens = $query->orderBy('datatriagem')->all();

        $labels = [];
        $counts = [];

        foreach ($triagens as $t) {
            // Atributo correto da BD
            $date = date('d-m-Y', strtotime($t->datatriagem));

            if (!isset($counts[$date])) {
                $counts[$date] = 0;
            }

            $counts[$date]++;
        }

        return [
            'labels' => array_keys($counts),
            'data'   => array_values($counts)
        ];
    }
}
