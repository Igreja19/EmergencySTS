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

    // NOTA: behaviors() removido porque herda do BaseActiveController

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    // INDEX
    public function actionIndex()
    {
        // O BaseActiveController garante que apenas Profissionais acedem aqui.
        // Removemos a lógica de filtrar "se for paciente", pois eles estão bloqueados.

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

        // Profissionais podem ver qualquer triagem.
        return $t;
    }

    // CREATE
    public function actionCreate()
    {
        // Nota: Como o BaseActiveController bloqueia Pacientes, esta ação
        // assume que é um Profissional a criar a triagem.

        $data = Yii::$app->request->post();
        $user = Yii::$app->user;

        // Se vier 'userprofile_id' no POST (criado por médico para um paciente), usa esse.
        // Caso contrário, usa o perfil do utilizador logado (comportamento original).
        if (isset($data['userprofile_id'])) {
            $profileId = $data['userprofile_id'];
        } else {
            $profile = UserProfile::findOne(['user_id'=>$user->id]);
            if (!$profile) throw new BadRequestHttpException("Sem perfil associado.");
            $profileId = $profile->id;
        }

        $t = new Triagem();
        $t->load($data, '');
        $t->userprofile_id = $profileId;
        $t->datatriagem = date("Y-m-d H:i:s");

        if (!$t->save()) return $t->errors;

        // Criar pulseira
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

        // MQTT Seguro
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish(
                    "triagem/criada/$t->id",
                    json_encode([
                        "evento" => "triagem_criada",
                        "triagem_id" => $t->id,
                        "pulseira_codigo" => $p->codigo,
                        "hora" => date("Y-m-d H:i:s")
                    ])
                );
            } catch (\Exception $e) {
                Yii::error("Erro MQTT Triagem Create: " . $e->getMessage());
            }
        }

        return ["status"=>"ok","triagem"=>$t,"pulseira"=>$p];
    }

    // UPDATE
    public function actionUpdate($id)
    {
        // Restrição extra: Apenas Enfermeiros e Médicos (Admins não costumam fazer triagem clinica)
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico'))
            throw new ForbiddenHttpException("Sem permissão para editar triagens.");

        $t = Triagem::findOne($id);
        if (!$t) throw new NotFoundHttpException("Triagem não encontrada.");

        $t->load(Yii::$app->request->post(), '');
        $t->save();

        // MQTT Seguro
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish(
                    "triagem/atualizada/$t->id",
                    json_encode(["evento"=>"triagem_atualizada","triagem_id"=>$t->id])
                );
            } catch (\Exception $e) {
                Yii::error("Erro MQTT Triagem Update: " . $e->getMessage());
            }
        }

        return $t;
    }

    // DELETE
    public function actionDelete($id)
    {
        // Restrição extra: Apenas Admin pode apagar
        if (!Yii::$app->user->can('admin'))
            throw new ForbiddenHttpException("Sem permissão.");

        $t = Triagem::findOne($id);
        if (!$t) throw new NotFoundHttpException();

        if ($t->pulseira_id) Pulseira::findOne($t->pulseira_id)->delete();

        $t->delete();

        // MQTT Seguro
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish(
                    "triagem/apagada/$id",
                    json_encode(["evento"=>"triagem_apagada","triagem_id"=>$id])
                );
            } catch (\Exception $e) {
                Yii::error("Erro MQTT Triagem Delete: " . $e->getMessage());
            }
        }

        return ["status"=>"success"];
    }

    // HISTÓRICO
    public function actionHistorico()
    {
        // Como o BaseActiveController bloqueia Pacientes,
        // isto mostra o histórico global para os profissionais.

        $query = Triagem::find()
            ->joinWith(['consulta'])
            ->with(['userprofile', 'pulseira'])
            ->where(['consulta.estado' => 'Encerrada'])
            ->orderBy(['triagem.datatriagem' => SORT_DESC]);

        $triagens = $query->all();

        // Serialização manual
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
}