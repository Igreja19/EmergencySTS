<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\Pulseira;
use common\models\PulseiraSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class PulseiraController extends Controller
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
     * Lista todas as pulseiras
     */
    public function actionIndex()
    {
        $searchModel = new PulseiraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);


        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Mostra uma pulseira
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Cria uma nova pulseira + NOTIFICAÇÕES AUTOMÁTICAS
     */
    public function actionCreate()
    {
        $model = new Pulseira();

        if ($this->request->isPost) {

            if ($model->load($this->request->post())) {

                // TEMPO AUTOMÁTICO
                $model->tempoentrada = date('Y-m-d H:i:s');

                // STATUS DEFAULT
                if (empty($model->status)) {
                    $model->status = 'Em espera';
                }

                if ($model->save()) {

                    // =====================================================
                    // 🔥 LIGAR A TRIAGEM AO ID DA PULSEIRA (REMOVER DA FILA)
                    // =====================================================
                    $triagem = \common\models\Triagem::find()
                        ->where(['userprofile_id' => $model->userprofile_id])
                        ->andWhere(['pulseira_id' => null])
                        ->one();

                    if ($triagem) {
                        $triagem->pulseira_id = $model->id;
                        $triagem->save(false);
                    }

                    // =====================================================
                    // 🔔 NOTIFICAÇÕES
                    // =====================================================
                    if ($model->userprofile_id && $model->userprofile) {

                        $userId = $model->userprofile_id;

                        // Notificação geral
                        Notificacao::enviar(
                            $userId,
                            "Pulseira atribuída",
                            "A pulseira do paciente " . $model->userprofile->nome . " foi criada.",
                            "Geral"
                        );

                        // Notificação crítica
                        if (in_array($model->prioridade, ["Vermelho", "Laranja"])) {
                            Notificacao::enviar(
                                $userId,
                                "Prioridade " . $model->prioridade,
                                "O paciente " . $model->userprofile->nome . " encontra-se com prioridade " . $model->prioridade . ".",
                                "Prioridade"
                            );
                        }
                    }

                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Atualiza pulseira + NOTIFICAÇÕES AUTOMÁTICAS
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $oldStatus = $model->status;
        $oldPrioridade = $model->prioridade;

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {

            $userId = $model->userprofile_id;

            /* ==============================
             * 🔥 NOTIFICAÇÕES DE STATUS
             * ==============================*/
            if ($oldStatus !== $model->status) {

                switch ($model->status) {

                    case "Em espera":
                        Notificacao::enviar(
                            $userId,
                            "Paciente em espera",
                            "O paciente " . $model->userprofile->nome . " foi colocado em espera.",
                            "Geral"
                        );
                        break;

                    case "Em atendimento":
                        Notificacao::enviar(
                            $userId,
                            "Paciente em atendimento",
                            "O paciente " . $model->userprofile->nome . " está a ser atendido.",
                            "Consulta"
                        );
                        break;

                    case "Atendido":
                        Notificacao::enviar(
                            $userId,
                            "Paciente atendido",
                            "O paciente " . $model->userprofile->nome . " foi atendido com sucesso.",
                            "Consulta"
                        );
                        break;
                }
            }

            /* ==============================
             * 🔥 NOTIFICAÇÕES DE PRIORIDADE
             * ==============================*/
            if ($oldPrioridade !== $model->prioridade) {

                if (in_array($model->prioridade, ["Vermelho", "Laranja"])) {
                    Notificacao::enviar(
                        $userId,
                        "Prioridade " . $model->prioridade,
                        "O paciente " . $model->userprofile->nome . " passou para prioridade " . $model->prioridade . ".",
                        "Prioridade"
                    );
                }
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Elimina pulseira
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Encontra a pulseira
     */
    protected function findModel($id)
    {
        if (($model = Pulseira::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException("A pulseira não existe.");
    }
}
