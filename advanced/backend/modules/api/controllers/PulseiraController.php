<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;
use backend\modules\api\controllers\BaseActiveController;
use common\models\Pulseira;

class PulseiraController extends BaseActiveController
{
    public $modelClass = 'common\models\Pulseira';
    public $enableCsrfValidation = false;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    // GET /api/pulseira
    public function actionIndex()
    {
        $user = Yii::$app->user;
        $query = Pulseira::find();

        // 1. SEGURANÇA: Se for Paciente, filtra apenas as pulseiras dele (via UserProfile)
        if ($user->can('paciente')) {
            $query->joinWith(['userprofile' => function ($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

        // 2. FILTROS (Disponíveis para todos, mas o paciente só filtra dentro das dele)
        $status = Yii::$app->request->get('status');
        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $prioridade = Yii::$app->request->get('prioridade');
        if ($prioridade) {
            $query->andWhere(['prioridade' => $prioridade]);
        }

        $query->orderBy(['tempoentrada' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    // GET /api/pulseira/{id}
    public function actionView($id)
    {
        // Carregar 'userprofile' para evitar queries extra na verificação
        $pulseira = Pulseira::find()
            ->where(['id' => $id])
            ->with('userprofile')
            ->one();

        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        // SEGURANÇA: Verificar se pertence ao utilizador logado
        if (Yii::$app->user->can('paciente')) {
            // Nota: Usar null coalescing (??) para evitar erros se userprofile for null
            $donoId = $pulseira->userprofile->user_id ?? null;

            if ($donoId != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta pulseira.");
            }
        }

        return $pulseira;
    }

    // PUT /api/pulseira/{id}
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais podem alterar pulseiras.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $data = Yii::$app->request->post();
        // O segundo argumento '' permite carregar sem nome do form (ex: Body raw JSON)
        $pulseira->load($data, '');

        if ($pulseira->save()) {

            $this->safeMqttPublish("pulseira/atualizada/{$pulseira->id}", [
                'evento'        => 'pulseira_atualizada',
                'pulseira_id'   => $pulseira->id,
                'prioridade'    => $pulseira->prioridade,
                'status'        => $pulseira->status,
                'userprofile_id'=> $pulseira->userprofile_id,
                'hora'          => date('Y-m-d H:i:s'),
            ]);

            return $pulseira;
        }

        return [
            'status' => 'error',
            'errors' => $pulseira->getErrors(),
        ];
    }

    // DELETE /api/pulseira/{id}
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Não encontrada.");
        }

        $pulseira->delete();

        $this->safeMqttPublish("pulseira/apagada/{$id}", [
            'evento'      => 'pulseira_apagada',
            'pulseira_id' => $id,
            'hora'        => date('Y-m-d H:i:s'),
        ]);

        return ['status' => 'success'];
    }

    protected function safeMqttPublish($topic, $payload)
    {
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;

        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish($topic, json_encode($payload));
            } catch (\Exception $e) {
                Yii::error("Erro MQTT ({$topic}): " . $e->getMessage());
            }
        }
    }
}