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

        // Se for Paciente, filtra apenas as pulseiras dele (via UserProfile)
        if ($user->can('paciente')) {
            $query->joinWith(['userprofile' => function ($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

        // (Disponíveis para todos, mas o paciente só filtra dentro das dele)
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
        // Carregar userprofile para evitar queries extra na verificação
        $pulseira = Pulseira::find()
            ->where(['id' => $id])
            ->with('userprofile')
            ->one();

        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        // Verificar se pertence ao utilizador logado
        if (Yii::$app->user->can('paciente')) {
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
        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $modoArquivar = Yii::$app->request->get('arquivar');

        // 3. EXECUÇÃO DE ARQUIVAR
        if ($modoArquivar == '1') {

            $pulseira->status = 'Atendido';

            if ($pulseira->save(false)) {

                // --- CORREÇÃO AQUI: Enviar para 'mosquitto/triagem' ---
                $this->safeMqttPublish("mosquitto/triagem", [
                    'titulo'        => 'Pulseira Arquivada',
                    'mensagem'      => "A pulseira {$pulseira->codigo} foi arquivada (Atendida).", // Adicionei 'arquivada' para ser claro
                    'evento'        => 'pulseira_atualizada',
                    'pulseira_id'   => $pulseira->id,
                    'status'        => 'Atendido',
                ]);

                return $pulseira;
            }
        }

        // Código normal para outras atualizações
        $data = Yii::$app->request->getBodyParams();
        $pulseira->load($data, '');
        if ($pulseira->save()) {
            return $pulseira;
        }

        return ['status' => 'error', 'msg' => 'Não foi possível gravar'];
    }


    // DELETE /api/pulseira/{id}
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Sem permissão para arquivar.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $pulseira->status = 'Atendido';

        if ($pulseira->save(false)) {

            $this->safeMqttPublish("mosquitto/triagem", [
                'titulo'        => 'Pulseira Removida',
                'mensagem'      => "A pulseira {$pulseira->codigo} foi removida da lista.",
                'evento'        => 'pulseira_atualizada',
                'pulseira_id'   => $pulseira->id,
                'status'        => 'Atendido',
            ]);

            return ['status' => 'success', 'message' => 'Pulseira arquivada com sucesso'];
        }

        return ['status' => 'error', 'message' => 'Erro ao arquivar'];
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