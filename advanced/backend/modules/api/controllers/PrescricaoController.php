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

    //  LISTAR PRESCRIÇÕES (GET /api/prescricao)

    public function actionIndex()
    {
        $user = Yii::$app->user;

        if ($user->can('enfermeiro') || $user->can('medico') || $user->can('admin')) {
            $query = Prescricao::find();
        } 
        // Pacientes
        else {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }
            
            // Precisamos de fazer um JOIN para chegar às prescrições do paciente
            // Prescricao -> Consulta -> UserProfile
            $query = Prescricao::find()
                ->joinWith('consulta')
                ->where(['consulta.userprofile_id' => $profile->id]);
        }

        $prescricoes = $query->orderBy(['dataprescricao' => SORT_DESC])->all();

        // Formatar dados
        $data = [];
        foreach ($prescricoes as $p) {
            $medicamentos = [];
            if ($p->getPrescricaomedicamentos()->exists()) {
                foreach ($p->prescricaomedicamentos as $pm) {
                    $medicamentos[] = $pm->medicamento->nome . ' (' . $pm->posologia . ')';
                }
            }

            $data[] = [
                'id' => $p->id,
                'data' => $p->dataprescricao,
                'medico' => 'Dr. Teste',
                'medicamentos' => $medicamentos,
                'consulta_id' => $p->consulta_id
            ];
        }

        return ['status' => 'success', 'total' => count($data), 'data' => $data];
    }

    //  VER UMA (GET /api/prescricao/{id})~

    public function actionView($id)
    {
        $prescricao = Prescricao::findOne($id);
        if (!$prescricao) throw new NotFoundHttpException("Prescrição não encontrada.");

        // Segurança (validar se pertence ao paciente)
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
                     'nome' => $pm->medicamento->nome,
                     'dosagem' => $pm->medicamento->dosagem,
                     'posologia' => $pm->posologia
                 ];
             }
        }

        return [
            'status' => 'success',
            'data' => [
                'id' => $prescricao->id,
                'data' => $prescricao->dataprescricao,
                'observacoes' => $prescricao->observacoes,
                'medicamentos' => $listaMedicamentos
            ]
        ];
    }

    //  CRIAR (POST /api/prescricao) 

    public function actionCreate()
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos.");
        }

        $data = Yii::$app->request->post();
        if (empty($data['consulta_id'])) throw new BadRequestHttpException("Falta consulta_id.");
        
        $consulta = Consulta::findOne($data['consulta_id']);
        if (!$consulta) throw new NotFoundHttpException("Consulta não encontrada.");

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $prescricao = new Prescricao();
            $prescricao->consulta_id = $consulta->id;
            $prescricao->dataprescricao = date('Y-m-d H:i:s');
            $prescricao->observacoes = $data['observacoes'] ?? '';

            if (!$prescricao->save()) throw new \Exception("Erro ao criar prescrição.");

            if (!empty($data['medicamentos']) && is_array($data['medicamentos'])) {
                foreach ($data['medicamentos'] as $item) {
                    $medicamento = Medicamento::findOne(['nome' => $item['nome'], 'dosagem' => $item['dosagem']]);
                    if (!$medicamento) {
                        $medicamento = new Medicamento();
                        $medicamento->nome = $item['nome'];
                        $medicamento->dosagem = $item['dosagem'];
                        $medicamento->save();
                    }
                    $linha = new Prescricaomedicamento();
                    $linha->prescricao_id = $prescricao->id;
                    $linha->medicamento_id = $medicamento->id;
                    $linha->posologia = $item['posologia']; 
                    $linha->save();
                }
            }
            $transaction->commit();
            return ['status' => 'success', 'data' => $prescricao];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 422;
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    //  APAGAR (DELETE /api/prescricao/{id})
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos/admin.");
        }

        $prescricao = Prescricao::findOne($id);
        if ($prescricao) {
            // Apagar linhas de medicamentos primeiro (se não houver CASCADE na BD)
            Prescricaomedicamento::deleteAll(['prescricao_id' => $id]);
            $prescricao->delete();
            
            return ['status' => 'success', 'message' => 'Prescrição anulada.'];
        }

        throw new NotFoundHttpException("Não encontrada.");
    }
}