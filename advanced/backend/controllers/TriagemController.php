<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\Pulseira;
use common\models\Triagem;
use common\models\TriagemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

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

        // Pulseiras pendentes = sem triagem e prioridade = pendente
        $pulseirasPendentes = \common\models\Pulseira::find()
            ->where(['prioridade' => 'Pendente'])
            ->orderBy(['tempoentrada' => SORT_ASC])
            ->all();

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

        // 🔥 Se for POST
        if ($this->request->isPost) {

            // Carregar dados do form
            if ($model->load($this->request->post())) {

                // =====================================================
                // ❌ VERIFICAR SE PACIENTE JÁ TEM PULSEIRA ATRIBUÍDA
                // =====================================================
                $pulseiraExistente = Pulseira::find()
                    ->where(['userprofile_id' => $model->userprofile_id])
                    ->andWhere(['in', 'prioridade', ['Vermelho','Laranja','Amarelo','Verde','Azul']])
                    ->one();

                if ($pulseiraExistente) {
                    /*if ($pulseiraExistente) {
                        die("PACIENTE JÁ TEM PULSEIRA ➜ prioridade = {$pulseiraExistente->prioridade}");
                    }*/

                    Yii::$app->session->setFlash(
                        'danger',
                        "Este paciente já tem uma pulseira atribuída. Não pode criar nova triagem."
                    );
                    return $this->redirect(['index']);
                }

                // =====================================================
                // ❌ VERIFICAR SE JÁ EXISTE TRIAGEM PENDENTE
                // =====================================================
                $triagemExistente = \common\models\Triagem::find()
                    ->where(['userprofile_id' => $model->userprofile_id])
                    ->andWhere(['pulseira_id' => null])
                    ->one();

                if ($triagemExistente) {
                    /*if ($triagemExistente) {
                        die("PACIENTE JÁ TEM TRIAGEM PENDENTE");
                    }*/
                    Yii::$app->session->setFlash(
                        'danger',
                        "Este paciente já tem uma triagem pendente. Deve atribuir uma pulseira antes de criar nova triagem."
                    );
                    return $this->redirect(['index']);
                }

                // =====================================================
                // 🔥 SE PASSOU NAS VALIDAÇÕES → GUARDAR TRIAGEM
                // =====================================================
                if ($model->save(false)) {

                    // =====================================================
                    // 🔥 ATRIBUIR A COR DA PULSEIRA SELECIONADA PELO ENFERMEIRO
                    // =====================================================
                    if (!empty($model->prioridade_pulseira)) {

                        $pulseira = \common\models\Pulseira::findOne($model->pulseira_id);

                        if ($pulseira) {
                            $pulseira->prioridade = $model->prioridade_pulseira;
                            $pulseira->status = "Em espera"; // opcional
                            $pulseira->save(false);
                        }
                    }

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
                    if ($model->prioridade_pulseira === "Vermelho" || $model->prioridade_pulseira === "Laranja") {
                        Notificacao::enviar(
                            $userId,
                            "Prioridade " . $model->prioridade_pulseira,
                            "O paciente " . $model->userprofile->nome . " encontra-se em prioridade " . $model->prioridade_pulseira . ".",
                            "Prioridade"
                        );
                    }

                    return $this->redirect(['index']);
                }
            }
        }

        // Carregar valores por defeito
        $model->loadDefaultValues();

        if ($model->iniciosintomas) {
            $model->iniciosintomas = date('Y-m-d\TH:i', strtotime($model->iniciosintomas));
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionPulseirasPorPaciente($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $pulseiras = \common\models\Pulseira::find()
            ->where(['userprofile_id' => $id])
            ->orderBy(['tempoentrada' => SORT_DESC])
            ->all();

        $result = [];

        foreach ($pulseiras as $p) {
            $result[] = [
                'id' => $p->id,
                'codigo' => $p->codigo . " — " . $p->prioridade . " — " . date("d/m/Y H:i", strtotime($p->tempoentrada))
            ];
        }

        return $result;
    }

    /**
     * Atualizar Triagem
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {

            // Atualizar prioridade da pulseira
            if (!empty($model->prioridade_pulseira)) {

                $pulseira = \common\models\Pulseira::findOne($model->pulseira_id);

                if ($pulseira) {
                    $pulseira->prioridade = $model->prioridade_pulseira;
                    $pulseira->status = "Em espera";
                    $pulseira->save(false);
                }
            }

            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
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
