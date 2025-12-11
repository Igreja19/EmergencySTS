<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\auth\QueryParamAuth;

use common\models\Prescricao;
use common\models\Prescricaomedicamento;
use common\models\Consulta;
use common\models\Medicamento;
use common\models\UserProfile;

class PrescricaoController extends ActiveController
{
    public $modelClass = 'common\models\Prescricao';
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

    // LISTAR PRESCRIÇÕES
    public function actionIndex()
    {
        $user = Yii::$app->user;

        if ($user->can('enfermeiro') || $user->can('medico') || $user->can('admin')) {
            $query = Prescricao::find();
        } else {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }

            $query = Prescricao::find()
                ->joinWith('consulta')
                ->where(['consulta.userprofile_id' => $profile->id]);
        }

        $prescricoes = $query->orderBy(['dataprescricao' => SORT_DESC])->all();

        $data = [];
        foreach ($prescricoes as $p) {
            $medicamentos = [];

            if ($p->getPrescricaomedicamentos()->exists()) {
                foreach ($p->prescricaomedicamentos as $pm) {
                    $medicamentos[] = $pm->medicamento->nome . ' (' . $pm->posologia . ')';
                }
            }

            $data[] = [
                'id'           => $p->id,
                'data'         => $p->dataprescricao,
                'medico'       => 'Dr. Teste',
                'medicamentos' => $medicamentos,
                'consulta_id'  => $p->consulta_id,
            ];
        }

        return ['status' => 'success', 'total' => count($data), 'data' => $data];
    }

    // VER UMA
    public function actionView($id)
    {
        $prescricao = Prescricao::findOne($id);
        if (!$prescricao) {
            throw new NotFoundHttpException("Prescrição não encontrada.");
        }

        $user = Yii::$app->user;
        if (!$user->can('medico') && !$user->can('admin')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if ($profile && $prescricao->consulta->userprofile_id != $profile->id) {
                throw new ForbiddenHttpException("Não tem permissão.");
            }
        }

        $listaMedicamentos = [];
        if ($prescricao->getPrescricaomedicamentos()->exists()) {
            foreach ($prescricao->prescricaomedicamentos as $pm) {
                $listaMedicamentos[] = [
                    'nome'      => $pm->medicamento->nome,
                    'dosagem'   => $pm->medicamento->dosagem,
                    'posologia' => $pm->posologia,
                ];
            }
        }

        return [
            'status' => 'success',
            'data'   => [
                'id'          => $prescricao->id,
                'data'        => $prescricao->dataprescricao,
                'observacoes' => $prescricao->observacoes,
                'medicamentos'=> $listaMedicamentos,
            ],
        ];
    }

    // CRIAR
    public function actionCreate()
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos.");
        }

        $data = Yii::$app->request->post();
        if (empty($data['consulta_id'])) {
            throw new BadRequestHttpException("Falta consulta_id.");
        }

        $consulta = Consulta::findOne($data['consulta_id']);
        if (!$consulta) {
            throw new NotFoundHttpException("Consulta não encontrada.");
        }

        $tx = Yii::$app->db->beginTransaction();

        try {
            $prescricao = new Prescricao();
            $prescricao->consulta_id   = $consulta->id;
            $prescricao->dataprescricao= date('Y-m-d H:i:s');
            $prescricao->observacoes   = $data['observacoes'] ?? '';

            if (!$prescricao->save()) {
                throw new \Exception("Erro ao criar prescrição.");
            }

            if (!empty($data['medicamentos']) && is_array($data['medicamentos'])) {
                foreach ($data['medicamentos'] as $item) {
                    $medicamento = Medicamento::findOne([
                        'nome'    => $item['nome'],
                        'dosagem' => $item['dosagem'],
                    ]);

                    if (!$medicamento) {
                        $medicamento = new Medicamento();
                        $medicamento->nome    = $item['nome'];
                        $medicamento->dosagem = $item['dosagem'];
                        $medicamento->save();
                    }

                    $linha = new Prescricaomedicamento();
                    $linha->prescricao_id  = $prescricao->id;
                    $linha->medicamento_id = $medicamento->id;
                    $linha->posologia      = $item['posologia'];
                    $linha->save();
                }
            }

            $tx->commit();

            // MQTT – prescrição criada
            Yii::$app->mqtt->publish(
                "prescricao/criada/{$prescricao->id}",
                json_encode([
                    'evento'       => 'prescricao_criada',
                    'prescricao_id'=> $prescricao->id,
                    'consulta_id'  => $prescricao->consulta_id,
                    'hora'         => date('Y-m-d H:i:s'),
                ])
            );

            return ['status' => 'success', 'data' => $prescricao];

        } catch (\Exception $e) {
            $tx->rollBack();
            Yii::$app->response->statusCode = 422;
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // DELETE
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos/admin.");
        }

        $prescricao = Prescricao::findOne($id);
        if (!$prescricao) {
            throw new NotFoundHttpException("Não encontrada.");
        }

        Prescricaomedicamento::deleteAll(['prescricao_id' => $id]);
        $prescricao->delete();

        // MQTT – prescrição anulada
        Yii::$app->mqtt->publish(
            "prescricao/apagada/{$id}",
            json_encode([
                'evento'       => 'prescricao_apagada',
                'prescricao_id'=> $id,
                'hora'         => date('Y-m-d H:i:s'),
            ])
        );

        return ['status' => 'success', 'message' => 'Prescrição anulada.'];
    }
}
