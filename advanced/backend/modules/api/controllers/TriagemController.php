<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\data\ActiveDataProvider;
use common\models\Triagem;
use common\models\Pulseira;

class TriagemController extends BaseActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user;

        $query = Triagem::find()
            ->with(['userprofile', 'pulseira'])
            ->orderBy(['datatriagem' => SORT_DESC]);

        if ($user->can('paciente')) {
            $query->joinWith(['userprofile' => function ($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

        if ($p = Yii::$app->request->get('pulseira_id')) {
            $query->andWhere(['pulseira_id' => $p]);
        }

        return new ActiveDataProvider([
            "query" => $query,
            "pagination" => false
        ]);
    }

    public function actionView($id)
    {
        $t = Triagem::find()
            ->where(['id' => $id])
            ->with(['userprofile', 'pulseira'])
            ->one();

        if (!$t) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        // Paciente só vê a SUA triagem específica
        if (Yii::$app->user->can('paciente')) {
            $donoId = $t->userprofile->user_id ?? null;
            if ($donoId != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta triagem.");
            }
        }

        return $t;
    }

    public function actionCreate()
    {
        $model = new Triagem();

        if ($this->request->isPost && $model->load($this->request->post())) {

            $tx = Yii::$app->db->beginTransaction();

            try {
                if (!$model->userprofile_id) {
                    throw new \Exception("O ID do utente é obrigatório.");
                }

                $model->datatriagem = date("Y-m-d H:i:s");

                if (!$model->save()) {
                    throw new \Exception("Erro ao guardar triagem. Verifique os dados.");
                }

                $p = new Pulseira([
                    'titulo'         => 'Nova Triagem',
                    'mensagem'        => "Nova pulseira criada",
                    "userprofile_id" => $model->userprofile_id,
                    "codigo"         => "P-" . strtoupper(substr(uniqid(), -5)), // Gera código único
                    "prioridade"     => "Pendente",
                    "status"         => "Em espera",
                    "tempoentrada"   => date('Y-m-d H:i:s')
                ]);

                if (!$p->save()) {
                    throw new \Exception("Erro ao gerar pulseira.");
                }

                $model->pulseira_id = $p->id;
                $model->save(false);

                $tx->commit();

                $this->safeMqttPublish("emergencysts/triagem", [
                    'titulo'          => 'Nova Triagem',
                    'mensagem'        => "Nova pulseira criada: {$p->codigo}. Em espera.",
                    "evento"          => "triagem_criada",
                    "triagem_id"      => $model->id,
                    "pulseira_codigo" => $p->codigo,
                    "hora"            => date("Y-m-d H:i:s")
                ]);

                Yii::$app->session->setFlash('success', 'Triagem criada com sucesso. Pulseira: ' . $p->codigo);
                return $this->redirect(['view', 'id' => $model->id]);

            } catch (\Exception $e) {
                $tx->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico')) {
            throw new ForbiddenHttpException("Sem permissão para editar triagens.");
        }

        $t = Triagem::findOne($id);
        if (!$t) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        $t->load(Yii::$app->request->post(), '');

        if ($t->save()) {
            $nomePaciente = $t->userprofile->nome ?? "Utente";
            $cor = $t->classificacao ?? "Prioridade definida";

            // Notificar MQTT (Atualização de dados da triagem)
            $this->safeMqttPublish("emergencysts/triagem", [
                'titulo'     => 'Triagem Atualizada',
                'mensagem'   => "Pac. {$nomePaciente} atualizada para {$cor}.",
                "evento"     => "triagem_atualizada",
                "triagem_id" => $t->id
            ]);

            return $t;
        }

        return $t->errors;
    }

    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin') && !Yii::$app->user->can('enfermeiro')) {
            throw new ForbiddenHttpException("Apenas Administradores ou Enfermeiros podem apagar triagens.");
        }

        $t = Triagem::findOne($id);
        if (!$t) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        $pulseiraId = $t->pulseira_id;

        try {
            if (!$t->delete()) {
                throw new \Exception("Não foi possível apagar.");
            }
        } catch (\Exception $e) {
            throw new ServerErrorHttpException("Erro ao apagar triagem. Verifique se existem consultas associadas.");
        }

        if ($pulseiraId) {
            $pulseira = Pulseira::findOne($pulseiraId);
            if ($pulseira) {
                $pulseira->delete();
            }
        }

        $this->safeMqttPublish("emergencysts/triagem", [
            'titulo'     => 'Triagem Removida',
            'mensagem'   => 'Uma triagem foi apagada do sistema.',
            "evento"     => "triagem_apagada", // O Frontend deve usar isto para remover da lista
            "triagem_id" => $id
        ]);

        return ["status" => "success"];
    }

    public function actionHistorico()
    {
        $user = Yii::$app->user;

        $query = Triagem::find()
            ->joinWith(['consulta'])
            ->with(['userprofile', 'pulseira'])
            ->where(['consulta.estado' => 'Encerrada'])
            ->orderBy(['triagem.datatriagem' => SORT_DESC]);

        if ($user->can('paciente')) {
            $query->joinWith(['userprofile' => function ($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
        }

        $triagens = $query->all();
        $result = [];

        foreach ($triagens as $t) {
            $result[] = [
                'id'                => $t->id,
                'datatriagem'       => $t->datatriagem,
                'motivoconsulta'    => $t->motivoconsulta,
                'queixaprincipal'   => $t->queixaprincipal,
                'consulta' => $t->consulta ? [
                    'id'     => $t->consulta->id,
                    'estado' => $t->consulta->estado,
                ] : null,
                'userprofile' => $t->userprofile ? [
                    'id'   => $t->userprofile->id,
                    'nome' => $t->userprofile->nome,
                ] : null,
                'pulseira' => $t->pulseira ? [
                    'id'         => $t->pulseira->id,
                    'prioridade' => $t->pulseira->prioridade,
                ] : null
            ];
        }

        return $result;
    }

    /**
     * Helper MQTT
     */
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