<?php

namespace backend\controllers;

use Yii;
use common\models\Medicamento;
use common\models\MedicamentoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MedicamentoController extends Controller
{
    public function behaviors()
    {
        return [
            // Aqui podes meter o AccessControl se quiseres proteger
        ];
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

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

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

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
