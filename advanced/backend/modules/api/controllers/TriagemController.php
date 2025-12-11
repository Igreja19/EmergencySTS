<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;

use common\models\Triagem;
use common\models\UserProfile;
use common\models\Pulseira;

require_once __DIR__ . '/../mqtt/phpMQTT.php';
use backend\modules\api\mqtt\phpMQTT;

class TriagemController extends ActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    private function publishMqtt($topic, $payload)
    {
        $server = '127.0.0.1';
        $port = 1883;
        $clientId = 'emergencysts-' . rand(1000,9999);

        $mqtt = new phpMQTT($server, $port, $clientId);

        if (!$mqtt->connect(true, NULL)) {
            return false;
        }

        $mqtt->publish($topic, $payload, 0);
        $mqtt->close();

        return true;
    }

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

        unset(
            $actions['index'],
            $actions['view'],
            $actions['create'],
            $actions['update'],
            $actions['delete']
        );

        return $actions;
    }

    //  TESTE MQTT
    public function actionTestMqtt()
    {
        $ok = $this->publishMqtt("triagem/teste", "Mensagem enviada do backend!");

        return [
            "status" => $ok ? "ok" : "erro"
        ];
    }

    //  INDEX
    public function actionIndex()
    {
        $user = Yii::$app->user;

        $query = Triagem::find()
            ->with(['userprofile', 'pulseira'])
            ->orderBy(['datatriagem' => SORT_DESC]);

        if ($pulseiraId = Yii::$app->request->get('pulseira_id')) {
            $query->andWhere(['pulseira_id' => $pulseiraId]);
        }

        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }
            $query->andWhere(['userprofile_id' => $profile->id]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    //  HISTÓRICO
    public function actionHistorico()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Triagem::find()
            ->joinWith(['consulta'])
            ->where(['consulta.estado' => 'encerrada'])
            ->with(['pulseira', 'userprofile'])
            ->orderBy(['triagem.datatriagem' => SORT_DESC]);

        $user = Yii::$app->user;
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            $query->andWhere(['triagem.userprofile_id' => $profile->id]);
        }

        return $query->all();
    }

    //  VIEW
    public function actionView($id)
    {
        $triagem = Triagem::find()
            ->with(['userprofile', 'pulseira'])
            ->where(['id' => $id])
            ->one();

        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        $user = Yii::$app->user;

        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if ($triagem->userprofile_id != $profile->id) {
                throw new ForbiddenHttpException("Não tem permissão.");
            }
        }

        return $triagem;
    }

    //  CREATE
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $user = Yii::$app->user;

        $profile = UserProfile::findOne(['user_id' => $user->id]);
        if (!$profile) {
            throw new BadRequestHttpException("Utilizador sem perfil associado.");
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $triagem = new Triagem();
            $triagem->load($data, '');
            $triagem->userprofile_id = $profile->id;
            $triagem->datatriagem = date('Y-m-d H:i:s');

            if (!$triagem->save()) {
                throw new \Exception(json_encode($triagem->errors));
            }

            $pulseira = new Pulseira([
                'userprofile_id' => $profile->id,
                'codigo' => 'P-' . strtoupper(substr(uniqid(), -5)),
                'prioridade' => 'Pendente',
                'status' => 'Em espera',
                'tempoentrada' => date('Y-m-d H:i:s')
            ]);

            if (!$pulseira->save()) {
                throw new \Exception(json_encode($pulseira->errors));
            }

            $triagem->pulseira_id = $pulseira->id;
            $triagem->save();

            $transaction->commit();

            // MQTT — nova triagem criada
            $this->publishMqtt(
                "triagem/atualizada/" . $triagem->id,
                json_encode([
                    "evento" => "triagem criada",
                    "triagem_id" => $triagem->id,
                    "pulseira_codigo" => $pulseira->codigo,
                    "prioridade" => $pulseira->prioridade,
                    "hora" => date('Y-m-d H:i:s'),
                ])
            );

            return [
                'status' => 'success',
                'triagem' => $triagem,
                'pulseira' => $pulseira
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 422;

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    //  UPDATE
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('enfermeiro') &&
            !Yii::$app->user->can('medico') &&
            !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Sem permissão.");
        }

        $triagem = Triagem::findOne($id);
        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        $triagem->load(Yii::$app->request->post(), '');
        $triagem->save();

        // MQTT — triagem atualizada
        $this->publishMqtt(
            "triagem/atualizada/" . $triagem->id,
            json_encode([
                "evento" => "triagem atualizada",
                "triagem_id" => $triagem->id,
                "dados" => $triagem->attributes,
                "hora" => date('Y-m-d H:i:s'),
            ])
        );

        return $triagem;
    }

    //  DELETE
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin') && !Yii::$app->user->can('enfermeiro')) {
            throw new ForbiddenHttpException("Sem permissão.");
        }

        $triagem = Triagem::findOne($id);
        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        if ($triagem->pulseira_id) {
            Pulseira::findOne($triagem->pulseira_id)->delete();
        }

        $triagem->delete();

        // MQTT — triagem removida
        $this->publishMqtt(
            "triagem/atualizada/" . $id,
            json_encode([
                "evento" => "triagem apagada",
                "triagem_id" => $id,
                "hora" => date('Y-m-d H:i:s'),
            ])
        );

        return ['status' => 'success', 'message' => 'Triagem apagada.'];
    }
}
