<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\Pulseira;
use common\models\PulseiraSearch;
use common\models\Triagem;
use common\models\UserProfile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

class PulseiraController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'only' => ['index','view','create','update','delete'],
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['admin', 'medico', 'enfermeiro'],
                        ],
                    ],
                    'denyCallback' => fn() => Yii::$app->response->redirect(['/site/login']),
                ],

                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new PulseiraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * ============================
     *   CREATE PULSEIRA
     * ============================
     */
    public function actionCreate()
    {
        $model = new Pulseira();
        $triagem = new Triagem();

        // Lista de pacientes para o dropdown
        $pacientes = \yii\helpers\ArrayHelper::map(
            UserProfile::find()->all(),
            'id',
            'nome'
        );

        if (Yii::$app->request->isPost) {

            if ($model->load(Yii::$app->request->post())) {

                // ================================
                // 1️⃣ Criar Pulseira (igual ao frontend)
                // ================================
                $model->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
                $model->prioridade = 'Pendente';
                $model->tempoentrada = date('Y-m-d H:i:s');
                $model->status = 'Em espera';

                if ($model->save(false)) {

                    // ================================
                    // 2️⃣ Criar Triagem automática
                    // ================================
                    $triagem->userprofile_id = $model->userprofile_id;
                    $triagem->pulseira_id = $model->id;
                    $triagem->datatriagem = date('Y-m-d H:i:s');

                    // Campos clínicos vazios (como no frontend)
                    $triagem->motivoconsulta = '';
                    $triagem->queixaprincipal = '';
                    $triagem->descricaosintomas = '';
                    $triagem->iniciosintomas = null;
                    $triagem->intensidadedor = 0;
                    $triagem->alergias = '';
                    $triagem->medicacao = '';

                    $triagem->save(false);

                    Yii::$app->session->setFlash('success', 'Pulseira pendente criada com triagem associada.');
                    return $this->redirect(['index']);
                }

                Yii::$app->session->setFlash('error', 'Erro ao criar a pulseira.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'pacientes' => $pacientes,
            'triagem' => $triagem,
        ]);
    }




    /**
     * ============================
     *   UPDATE
     * ============================
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * DELETE
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * FIND MODEL
     */
    protected function findModel($id)
    {
        if (($model = Pulseira::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException("A pulseira não existe.");
    }
}
