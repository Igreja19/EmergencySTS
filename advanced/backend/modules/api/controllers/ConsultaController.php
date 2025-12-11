<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\auth\QueryParamAuth;

use common\models\Consulta;
use common\models\UserProfile;
use common\models\Triagem;

// MQTT
require_once __DIR__ . '/../mqtt/phpMQTT.php';
use backend\modules\api\mqtt\phpMQTT;

class ConsultaController extends ActiveController
{
    public $modelClass = 'common\models\Consulta';
    public $enableCsrfValidation = false;

    // ---------------------------------------------------------
    // MQTT FUNCTION
    // ---------------------------------------------------------
    private function publishMqtt($topic, $payload)
    {
        $server = '127.0.0.1';
        $port = 1883;
        $clientId = 'emergencysts-consulta-' . rand(1000,9999);

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
    // HISTÓRICO
    // ---------------------------------------------------------
    public function actionHistorico($id)
    {
        $profile = UserProfile::findOne($id);
        if (!$profile) {
            throw new NotFoundHttpException("Perfil de utilizador não encontrado.");
        }

        $user = Yii::$app->user;
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $myProfile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$myProfile || $myProfile->id != $id) {
                throw new ForbiddenHttpException("Sem permissão.");
            }
        }

        $consultas = Consulta::find()
            ->where(['userprofile_id' => $id])
            ->orderBy(['data_consulta' => SORT_DESC])
            ->all();

        $data = [];
        foreach ($consultas as $consulta) {
            $data[] = [
                'id' => $consulta->id,
                'data' => $consulta->data_consulta,
                'estado' => $consulta->estado,
                'observacoes' => $consulta->observacoes,
                'relatorio_pdf' => $consulta->relatorio_pdf,
                'triagem' => $consulta->triagem ? [
                    'queixa' => $consulta->triagem->queixaprincipal,
                    'prioridade' => $consulta->triagem->prioridadeatribuida ?? 'N/A'
                ] : null,
            ];
        }

        return [
            'status' => 'success',
            'total' => count($data),
            'data' => $data
        ];
    }

    // ---------------------------------------------------------
    // CREATE (INICIAR CONSULTA)
    // ---------------------------------------------------------
    public function actionCreate()
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem iniciar consultas.");
        }

        $data = Yii::$app->request->post();

        if (empty($data['triagem_id'])) {
            throw new BadRequestHttpException("É necessário enviar 'triagem_id'.");
        }

        $triagem = Triagem::findOne($data['triagem_id']);
        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        $consulta = new Consulta();
        $consulta->triagem_id = $triagem->id;
        $consulta->userprofile_id = $triagem->userprofile_id;
        $consulta->data_consulta = date('Y-m-d H:i:s');
        $consulta->estado = 'Em curso';

        if (isset($data['observacoes'])) {
            $consulta->observacoes = $data['observacoes'];
        }

        if ($consulta->save()) {

            // Atualizar pulseira → Em atendimento
            if ($triagem->pulseira) {
                $triagem->pulseira->status = 'Em atendimento';
                $triagem->pulseira->save();
            }

            // -------------------------------------------------
            // MQTT – consulta criada
            // -------------------------------------------------
            $this->publishMqtt(
                "consulta/criada/" . $consulta->id,
                json_encode([
                    "evento" => "consulta_criada",
                    "consulta_id" => $consulta->id,
                    "userprofile_id" => $consulta->userprofile_id,
                    "triagem_id" => $consulta->triagem_id,
                    "estado" => $consulta->estado,
                    "hora" => date('Y-m-d H:i:s'),
                ])
            );

            return [
                'status' => 'success',
                'message' => 'Consulta iniciada.',
                'data' => $consulta
            ];
        }

        Yii::$app->response->statusCode = 422;
        return ['status' => 'error', 'errors' => $consulta->getErrors()];
    }

    // ---------------------------------------------------------
    // UPDATE (ENCERRAR OU ALTERAR CONSULTA)
    // ---------------------------------------------------------
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem atualizar consultas.");
        }

        $consulta = Consulta::findOne($id);
        if (!$consulta) {
            throw new NotFoundHttpException("Consulta não encontrada.");
        }

        $data = Yii::$app->request->getBodyParams() ?: Yii::$app->request->post();

        if (isset($data['observacoes'])) {
            $consulta->observacoes = $data['observacoes'];
        }
        if (isset($data['estado'])) {
            $consulta->estado = $data['estado'];
        }

        if ($consulta->save()) {

            // -------------------------------------------------
            // MQTT – consulta atualizada
            // -------------------------------------------------
            $this->publishMqtt(
                "consulta/atualizada/" . $consulta->id,
                json_encode([
                    "evento" => "consulta_atualizada",
                    "consulta_id" => $consulta->id,
                    "estado" => $consulta->estado,
                    "hora" => date('Y-m-d H:i:s')
                ])
            );

            // Se ENCERRADA → atualizar pulseira para "Atendido"
            if ($consulta->estado === 'Encerrada') {
                $triagem = Triagem::findOne($consulta->triagem_id);

                if ($triagem && $triagem->pulseira) {
                    $triagem->pulseira->status = 'Atendido';
                    $triagem->pulseira->save();

                    // MQTT: pulseira atualizada
                    $this->publishMqtt(
                        "pulseira/" . $triagem->pulseira->id,
                        json_encode([
                            "evento" => "pulseira_atualizada",
                            "pulseira_id" => $triagem->pulseira->id,
                            "status" => "Atendido",
                            "hora" => date('Y-m-d H:i:s')
                        ])
                    );

                    // MQTT: consulta encerrada
                    $this->publishMqtt(
                        "consulta/encerrada/" . $consulta->id,
                        json_encode([
                            "evento" => "consulta_encerrada",
                            "consulta_id" => $consulta->id,
                            "hora" => date('Y-m-d H:i:s')
                        ])
                    );
                }
            }

            return [
                'status' => 'success',
                'message' => "Consulta atualizada.",
                'data' => $consulta
            ];
        }

        return ['status' => 'error', 'errors' => $consulta->getErrors()];
    }
}
