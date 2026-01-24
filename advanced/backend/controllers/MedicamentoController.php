<?php

namespace backend\controllers;

use Yii;
use common\models\Medicamento;
use common\models\MedicamentoSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MedicamentoController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
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
        $searchModel = new MedicamentoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Medicamento();

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {

            try {
                if (Yii::$app->has('mqtt')) {
                    Yii::$app->mqtt->publish(
                        "medicamento/criado/{$model->id}",
                        json_encode([
                            'evento' => 'medicamento_criado_backend',
                            'medicamento_id' => $model->id,
                            'nome' => $model->nome,
                            'dosagem' => $model->dosagem,
                            'hora' => date('Y-m-d H:i:s')
                        ])
                    );
                }
            } catch (\Exception $e) {
                Yii::warning("Falha MQTT (Create Medicamento): " . $e->getMessage());
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {

            try {
                if (Yii::$app->has('mqtt')) {
                    Yii::$app->mqtt->publish(
                        "medicamento/atualizado/{$model->id}",
                        json_encode([
                            'evento' => 'medicamento_atualizado_backend',
                            'medicamento_id' => $model->id,
                            'nome' => $model->nome,
                            'dosagem' => $model->dosagem,
                            'hora' => date('Y-m-d H:i:s')
                        ])
                    );
                }
            } catch (\Exception $e) {
                Yii::warning("Falha MQTT (Update Medicamento): " . $e->getMessage());
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "medicamento/apagado/{$id}",
                    json_encode([
                        'evento' => 'medicamento_apagado_backend',
                        'medicamento_id' => $id,
                        'hora' => date('Y-m-d H:i:s')
                    ])
                );
            }
        } catch (\Exception $e) {
            Yii::warning("Falha MQTT (Delete Medicamento): " . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Medicamento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('O medicamento não existe.');
    }
}