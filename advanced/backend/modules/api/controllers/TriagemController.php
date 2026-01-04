<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\data\ActiveDataProvider;
use backend\modules\api\controllers\BaseActiveController;

use common\models\Triagem;
use common\models\UserProfile;
use common\models\Pulseira;

class TriagemController extends BaseActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    // INDEX (Ver todas as triagens)
    public function actionIndex()
    {
        // SEGURANÇA: Pacientes não podem ver a lista de admissões do dia.
        if (Yii::$app->user->can('paciente')) {
            throw new ForbiddenHttpException("Acesso reservado a profissionais de saúde.");
        }

        $query = Triagem::find()->with(['userprofile', 'pulseira'])
            ->orderBy(['datatriagem' => SORT_DESC]);

        if ($p = Yii::$app->request->get('pulseira_id')) {
            $query->andWhere(['pulseira_id' => $p]);
        }

        return new ActiveDataProvider([
            "query" => $query,
            "pagination" => false
        ]);
    }

    // VIEW
    public function actionView($id)
    {
        $t = Triagem::find()->with(['userprofile','pulseira'])->where(['id'=>$id])->one();
        if (!$t) throw new NotFoundHttpException("Triagem não encontrada.");

        // SEGURANÇA: Paciente só vê a SUA triagem.
        if (Yii::$app->user->can('paciente')) {
            if ($t->userprofile->user_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta triagem.");
            }
        }

        return $t;
    }

    // CREATE (Fazer triagem)
    public function actionCreate()
    {
        // SEGURANÇA: Bloquear auto-triagem por pacientes.
        if (Yii::$app->user->can('paciente')) {
            throw new ForbiddenHttpException("Apenas profissionais podem registar triagens.");
        }

        $data = Yii::$app->request->post();
        $user = Yii::$app->user;

        if (isset($data['userprofile_id'])) {
            $profileId = $data['userprofile_id'];
        } else {
            // Fallback para o user logado (apenas se for médico a criar para si próprio, raro mas possível)
            $profile = UserProfile::findOne(['user_id'=>$user->id]);
            if (!$profile) throw new BadRequestHttpException("Sem perfil associado.");
            $profileId = $profile->id;
        }

        $t = new Triagem();
        $t->load($data, '');
        $t->userprofile_id = $profileId;
        $t->datatriagem = date("Y-m-d H:i:s");

        if (!$t->save()) return $t->errors;

        // Criar pulseira automática
        $p = new Pulseira([
            "userprofile_id" => $profileId,
            "codigo" => "P-" . strtoupper(substr(uniqid(), -5)),
            "prioridade" => "Pendente",
            "status" => "Em espera",
            "tempoentrada" => date('Y-m-d H:i:s')
        ]);
        $p->save();

        $t->pulseira_id = $p->id;
        $t->save();

        // MQTT
        $this->safeMqttPublish("triagem/criada/$t->id", [
            "evento" => "triagem_criada",
            "triagem_id" => $t->id,
            "pulseira_codigo" => $p->codigo,
            "hora" => date("Y-m-d H:i:s")
        ]);

        return ["status"=>"ok","triagem"=>$t,"pulseira"=>$p];
    }

    // UPDATE
    public function actionUpdate($id)
    {
        // Apenas Staff pode editar
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico'))
            throw new ForbiddenHttpException("Sem permissão para editar triagens.");

        $t = Triagem::findOne($id);
        if (!$t) throw new NotFoundHttpException("Triagem não encontrada.");

        $t->load(Yii::$app->request->post(), '');
        $t->save();

        // MQTT
        $this->safeMqttPublish("triagem/atualizada/$t->id", ["evento"=>"triagem_atualizada","triagem_id"=>$t->id]);

        return $t;
    }

    // DELETE
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin'))
            throw new ForbiddenHttpException("Sem permissão.");

        $t = Triagem::findOne($id);
        if (!$t) throw new NotFoundHttpException();

        if ($t->pulseira_id) Pulseira::findOne($t->pulseira_id)->delete();

        $t->delete();

        // MQTT
        $this->safeMqttPublish("triagem/apagada/$id", ["evento"=>"triagem_apagada","triagem_id"=>$id]);

        return ["status"=>"success"];
    }

    // HISTÓRICO GLOBAL
    public function actionHistorico()
    {
        // SEGURANÇA: Bloquear Paciente de ver histórico geral do hospital
        if (Yii::$app->user->can('paciente')) {
            throw new ForbiddenHttpException("Utilize a rota de histórico pessoal.");
        }

        $query = Triagem::find()
            ->joinWith(['consulta'])
            ->with(['userprofile', 'pulseira'])
            ->where(['consulta.estado' => 'Encerrada'])
            ->orderBy(['triagem.datatriagem' => SORT_DESC]);

        $triagens = $query->all();

        // Formatação manual (igual ao que tinhas)
        $result = [];
        foreach ($triagens as $t) {
            $result[] = [
                'id'                => $t->id,
                'datatriagem'       => $t->datatriagem,
                'motivoconsulta'    => $t->motivoconsulta,
                'queixaprincipal'   => $t->queixaprincipal,
                'descricaosintomas' => $t->descricaosintomas,
                'iniciosintomas'    => $t->iniciosintomas,
                'alergias'          => $t->alergias,
                'medicacao'         => $t->medicacao,
                'consulta' => $t->consulta ? [
                    'id'        => $t->consulta->id,
                    'estado'    => $t->consulta->estado,
                    'data'      => $t->consulta->data_consulta,
                ] : null,
                'userprofile' => $t->userprofile ? [
                    'id'        => $t->userprofile->id,
                    'nome'      => $t->userprofile->nome,
                    'sns'       => $t->userprofile->sns,
                    'telefone'  => $t->userprofile->telefone,
                    'email'     => $t->userprofile->email,
                ] : null,
                'pulseira' => $t->pulseira ? [
                    'id'          => $t->pulseira->id,
                    'codigo'      => $t->pulseira->codigo,
                    'prioridade'  => $t->pulseira->prioridade,
                    'status'      => $t->pulseira->status,
                    'tempoentrada'=> $t->pulseira->tempoentrada,
                ] : null
            ];
        }

        return $result;
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