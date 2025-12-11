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

class TriagemController extends ActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $b = parent::behaviors();
        unset($b['authenticator']);

        $b['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        $b['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $b;
    }

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    //--------------------------
    // TESTE MQTT
    //--------------------------
    public function actionTestMqtt()
    {
        Yii::$app->mqtt->publish("triagem/teste", json_encode(["msg" => "OK"]));
        return ["status" => "mqtt sent"];
    }

    //--------------------------
    // INDEX
    //--------------------------
    public function actionIndex()
    {
        $query = Triagem::find()->with(['userprofile', 'pulseira'])
            ->orderBy(['datatriagem' => SORT_DESC]);

        if ($p = Yii::$app->request->get('pulseira_id')) {
            $query->andWhere(['pulseira_id' => $p]);
        }

        $user = Yii::$app->user;

        if (!$user->can('admin') && !$user->can('enfermeiro') && !$user->can('medico')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            $query->andWhere(['userprofile_id' => $profile->id]);
        }

        return new ActiveDataProvider([
            "query" => $query,
            "pagination" => false
        ]);
    }

    //--------------------------
    // VIEW
    //--------------------------
    public function actionView($id)
    {
        $t = Triagem::find()->with(['userprofile','pulseira'])->where(['id'=>$id])->one();

        if (!$t) throw new NotFoundHttpException("Triagem não encontrada.");

        $user = Yii::$app->user;
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id'=>$user->id]);
            if ($t->userprofile_id != $profile->id)
                throw new ForbiddenHttpException("Sem permissão.");
        }

        return $t;
    }

    //--------------------------
    // CREATE
    //--------------------------
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $user = Yii::$app->user;
        $profile = UserProfile::findOne(['user_id'=>$user->id]);

        if (!$profile) throw new BadRequestHttpException("Sem perfil associado.");

        $t = new Triagem();
        $t->load($data, '');
        $t->userprofile_id = $profile->id;
        $t->datatriagem = date("Y-m-d H:i:s");
        if (!$t->save()) return $t->errors;

        // Criar pulseira
        $p = new Pulseira([
            "userprofile_id" => $profile->id,
            "codigo" => "P-" . strtoupper(substr(uniqid(), -5)),
            "prioridade" => "Pendente",
            "status" => "Em espera",
            "tempoentrada" => date('Y-m-d H:i:s')
        ]);
        $p->save();

        $t->pulseira_id = $p->id;
        $t->save();

        //----------------------
        // MQTT - triagem criada
        //----------------------
        Yii::$app->mqtt->publish(
            "triagem/criada/$t->id",
            json_encode([
                "evento" => "triagem_criada",
                "triagem_id" => $t->id,
                "pulseira_codigo" => $p->codigo,
                "hora" => date("Y-m-d H:i:s")
            ])
        );

        return ["status"=>"ok","triagem"=>$t,"pulseira"=>$p];
    }

    //--------------------------
    // UPDATE
    //--------------------------
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico'))
            throw new ForbiddenHttpException("Sem permissão.");

        $t = Triagem::findOne($id);
        if (!$t) throw new NotFoundHttpException("Triagem não encontrada.");

        $t->load(Yii::$app->request->post(), '');
        $t->save();

        Yii::$app->mqtt->publish(
            "triagem/atualizada/$t->id",
            json_encode(["evento"=>"triagem_atualizada","triagem_id"=>$t->id])
        );

        return $t;
    }

    //--------------------------
    // DELETE
    //--------------------------
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin'))
            throw new ForbiddenHttpException("Sem permissão.");

        $t = Triagem::findOne($id);
        if (!$t) throw new NotFoundHttpException();

        if ($t->pulseira_id) Pulseira::findOne($t->pulseira_id)->delete();

        $t->delete();

        Yii::$app->mqtt->publish(
            "triagem/apagada/$id",
            json_encode(["evento"=>"triagem_apagada","triagem_id"=>$id])
        );

        return ["status"=>"success"];
    }
    public function actionHistorico()
    {
        $user = Yii::$app->user;

        // Carregar triagens + pulseira + consulta
        $query = Triagem::find()
            ->joinWith(['consulta'])        // NECESSÁRIO PARA FILTRAR CONSULTAS
            ->with(['userprofile', 'pulseira'])
            ->where(['consulta.estado' => 'Encerrada'])  // Só triagens com consulta encerrada
            ->orderBy(['triagem.datatriagem' => SORT_DESC]);

        // Se o user NÃO for admin/enfermeiro/medico → mostrar só as dele
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {

            $profile = UserProfile::findOne(['user_id' => $user->id]);

            if (!$profile) {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }

            $query->andWhere(['triagem.userprofile_id' => $profile->id]);
        }

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

                // relação consulta (agora disponível)
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
