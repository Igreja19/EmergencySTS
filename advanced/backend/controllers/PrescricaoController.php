<?php

namespace backend\controllers;

use Yii;
use common\models\Prescricao;
use common\models\PrescricaoSearch;
use common\models\Consulta;
use common\models\Medicamento;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class PrescricaoController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new PrescricaoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Prescricao();

        $consultas = \common\models\Consulta::find()
            ->select(['id'])
            ->indexBy('id')
            ->column();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Prescrição criada com sucesso!');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'consultas' => $consultas,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $medicamentos = Medicamento::find()
            ->select(['nome'])
            ->indexBy('id')
            ->column();

        $consultas = Consulta::find()
            ->select(['id'])
            ->indexBy('id')
            ->column();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Prescrição atualizada com sucesso!');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'medicamentos' => $medicamentos,
            'consultas' => $consultas, // ✅ também aqui
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Prescrição eliminada com sucesso.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Prescricao::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('A prescrição solicitada não existe.');
    }
}
