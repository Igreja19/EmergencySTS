<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\QueryParamAuth;
use common\models\Medicamento;

class MedicamentoController extends ActiveController
{
    public $modelClass = 'common\models\Medicamento';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }


    //  PESQUISAR MEDICAMENTOS (GET /api/medicamento?nome=Ben)
   
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $nome = $request->get('nome'); // Parâmetro de pesquisa

        $query = Medicamento::find();

        if ($nome) {
            // Pesquisa por nome (ex: "Ben" encontra "Ben-u-ron")
            $query->where(['like', 'nome', $nome]);
        }

        // Limite de 20 para não sobrecarregar a App
        $medicamentos = $query->limit(20)->all();

        return [
            'status' => 'success',
            'total' => count($medicamentos),
            'data' => $medicamentos
        ];
    }

   
    //  CRIAR / GERIR (Apenas Admin)

    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem gerir o catálogo de medicamentos.");
        }
        
        $model = new Medicamento();
        $model->load(Yii::$app->request->post(), '');
        
        if ($model->save()) {
            return ['status' => 'success', 'data' => $model];
        }
        return ['status' => 'error', 'errors' => $model->errors];
    }

}