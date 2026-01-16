<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;
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
        // Desativamos as ações padrão para as personalizarmos abaixo
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    // GET /api/pulseiras
    public function actionIndex()
    {
        $user = Yii::$app->user;
        $query = Pulseira::find();

        // 1. Se for Paciente, vê apenas as suas
        if ($user->can('paciente')) {
            $query->joinWith(['userprofile' => function ($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

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

    // GET /api/pulseiras/{id}
    public function actionView($id)
    {
        $pulseira = Pulseira::find()
            ->where(['id' => $id])
            ->with('userprofile')
            ->one();

        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        // Segurança: Paciente só vê a sua
        if (Yii::$app->user->can('paciente')) {
            $donoId = $pulseira->userprofile->user_id ?? null;
            if ($donoId != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta pulseira.");
            }
        }

        return $pulseira;
    }

    // PUT /api/pulseiras/{id}
    public function actionUpdate($id)
    {
        // 1. SEGURANÇA (Reintroduzida): Apenas Staff pode editar
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais de saúde podem alterar pulseiras.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $modoArquivar = Yii::$app->request->get('arquivar');

        if ($modoArquivar == '1') {
            $pulseira->status = 'Atendido';

            if ($pulseira->save(false)) {
                // Publica no tópico que FUNCIONA
                $this->safeMqttPublish("emergencysts/triagem", [
                    'titulo'        => 'Pulseira Arquivada',
                    'mensagem'      => "A pulseira {$pulseira->codigo} foi arquivada (Atendida).",
                    'evento'        => 'pulseira_atualizada', // Mantido evento genérico para refresh do grid
                    'pulseira_id'   => $pulseira->id,
                    'status'        => 'Atendido',
                ]);
                return $pulseira;
            }
        }

        $data = Yii::$app->request->getBodyParams();
        $pulseira->load($data, '');

        if ($pulseira->save()) {
            // Notifica atualização de dados (cor, prioridade, etc)
            $this->safeMqttPublish("emergencysts/triagem", [
                'titulo'        => 'Pulseira Atualizada',
                'mensagem'      => "A pulseira {$pulseira->codigo} foi atualizada.",
                'evento'        => 'pulseira_atualizada',
                'pulseira_id'   => $pulseira->id,
                'status'        => $pulseira->status,
                'prioridade'    => $pulseira->prioridade
            ]);

            return $pulseira;
        }

        return ['status' => 'error', 'errors' => $pulseira->errors];
    }

    // DELETE /api/pulseiras/{id}
    public function actionDelete($id)
    {
        // Segurança
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Sem permissão para arquivar.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        // Soft Delete (Muda status para Atendido)
        $pulseira->status = 'Atendido';

        if ($pulseira->save(false)) {

            $this->safeMqttPublish("emergencysts/triagem", [
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

    /**
     * Helper para publicar no MQTT sem crashar a API se o broker estiver offline
     */
    protected function safeMqttPublish($topic, $payload)
    {
        // Verifica se o componente existe e se o parâmetro global permite
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;

        if ($mqttEnabled && Yii::$app->has('mqtt')) {
            try {
                Yii::$app->mqtt->publish($topic, json_encode($payload));
            } catch (\Exception $e) {
                Yii::error("Erro ao publicar MQTT ({$topic}): " . $e->getMessage());
            }
        }
    }
}