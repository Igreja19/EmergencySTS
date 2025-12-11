<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\QueryParamAuth;
use common\models\Medicamento;

// MQTT
require_once __DIR__ . '/../mqtt/phpMQTT.php';
use backend\modules\api\mqtt\phpMQTT;

class MedicamentoController extends ActiveController
{
    public $modelClass = 'common\models\Medicamento';
    public $enableCsrfValidation = false;

    // ---------------------------------------------------------
    // MQTT FUNCTION
    // ---------------------------------------------------------
    private function publishMqtt($topic, $payload)
    {
        $server = '127.0.0.1';
        $port = 1883;
        $clientId = 'emergencysts-medicamento-' . rand(1000,9999);

        $mqtt = new phpMQTT($server, $port, $clientId);

        if (!$mqtt->connect(true, NULL)) {
            return false;
        }

        $mqtt->publish($topic, $payload, 0);
        $mqtt->close();
        return true;
    }

    // ---------------------------------------------------------
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

    // ---------------------------------------------------------
    // PESQUISA DE MEDICAMENTOS (GET /api/medicamento?nome=Ben)
    // ---------------------------------------------------------
    public function actionIndex()
    {
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

    // ---------------------------------------------------------
    // CRIAR MEDICAMENTO (APENAS ADMIN)
    // ---------------------------------------------------------
    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem gerir o catálogo de medicamentos.");
        }

        $model = new Medicamento();
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {

            // MQTT — medicamento criado
            $this->publishMqtt(
                "medicamento/criado/" . $model->id,
                json_encode([
                    "evento" => "medicamento_criado",
                    "medicamento_id" => $model->id,
                    "nome" => $model->nome,
                    "descricao" => $model->descricao ?? null,
                    "hora" => date('Y-m-d H:i:s'),
                ])
            );

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
