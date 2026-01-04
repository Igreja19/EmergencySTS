<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use backend\modules\api\controllers\BaseActiveController;
use common\models\Medicamento;

class MedicamentoController extends BaseActiveController
{
    public $modelClass = 'common\models\Medicamento';
    public $enableCsrfValidation = false;

    // NOTA: behaviors() removido porque herda do BaseActiveController

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    // PESQUISA DE MEDICAMENTOS (GET /api/medicamento?nome=Ben)
    public function actionIndex()
    {
        // O BaseActiveController garante que apenas Admin/Médico/Enfermeiro acedem aqui.
        if (Yii::$app->user->can('paciente')) {
            throw new ForbiddenHttpException("Acesso reservado a staff.");
        }

        $nome = Yii::$app->request->get('nome');
        $query = Medicamento::find();

        if ($nome) {
            $query->where(['like', 'nome', $nome]);
        }

        $medicamentos = $query->limit(40)->all();

        return [
            'status' => 'success',
            'total' => count($medicamentos),
            'data' => $medicamentos
        ];
    }

    // CRIAR MEDICAMENTO (APENAS ADMIN)
    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem gerir o catálogo de medicamentos.");
        }

        $model = new Medicamento();
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {

            // MQTT Seguro
            $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
            if ($mqttEnabled && isset(Yii::$app->mqtt)) {
                try {
                    Yii::$app->mqtt->publish(
                        "medicamento/criado/" . $model->id,
                        json_encode([
                            "evento" => "medicamento_criado",
                            "medicamento_id" => $model->id,
                            "nome" => $model->nome,
                            "descricao" => $model->descricao ?? null,
                            "hora" => date('Y-m-d H:i:s'),
                        ])
                    );
                } catch (\Exception $e) {
                    Yii::error("Erro MQTT Medicamento Create: " . $e->getMessage());
                }
            }

            return [
                'status' => 'success',
                'message' => 'Medicamento criado com sucesso.',
                'data' => $model
            ];
        }

        return [
            'status' => 'error',
            'errors' => $model->errors
        ];
    }
}