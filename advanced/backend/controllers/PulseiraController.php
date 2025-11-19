<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\Pulseira;
use common\models\PulseiraSearch;
use common\models\Triagem;
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

        // Triagens sem pulseira atribuída
        $triagensPendentes = Triagem::find()
            ->where(['pulseira_id' => null])
            ->all();

        $triagensDropdown = \yii\helpers\ArrayHelper::map(
            $triagensPendentes,
            'id',
            fn($t) => "Triagem #{$t->id} — {$t->userprofile->nome} — {$t->motivoconsulta}"
        );

        if (Yii::$app->request->isPost) {

            // CORRETO: vem como campo independente (não do model Pulseira)
            $triagem_id = Yii::$app->request->post('triagem_id');

            if (!$triagem_id) {
                Yii::$app->session->setFlash('error', 'Selecione uma triagem.');
                return $this->redirect(['create']);
            }

            $triagem = Triagem::findOne($triagem_id);

            if (!$triagem) {
                Yii::$app->session->setFlash('error', 'Triagem não encontrada.');
                return $this->redirect(['create']);
            }

            // Preencher dados automáticos
            $model->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
            $model->prioridade = 'Pendente';
            $model->status = 'Em espera';
            $model->tempoentrada = date('Y-m-d H:i:s');
            $model->userprofile_id = $triagem->userprofile_id;

            if ($model->save(false)) {

                // RELACIONAR com a triagem
                $triagem->pulseira_id = $model->id;
                $triagem->save(false);

                Yii::$app->session->setFlash('success', 'Pulseira criada com sucesso.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'triagensDropdown' => $triagensDropdown,
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
