<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use backend\modules\api\controllers\BaseActiveController;

use common\models\Prescricao;
use common\models\Prescricaomedicamento;
use common\models\Consulta;
use common\models\Medicamento;
use common\models\UserProfile;

class PrescricaoController extends BaseActiveController
{
    public $modelClass = 'common\models\Prescricao';
    public $enableCsrfValidation = false;

    // NOTA: behaviors() removido porque herda do BaseActiveController

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    // LISTAR PRESCRIÇÕES
    public function actionIndex()
    {
        // O BaseActiveController garante que apenas Admin, Médico ou Enfermeiro chegam aqui.
        // Por isso, mostramos todas as prescrições (ou filtrar se necessário).

       $user = Yii::$app->user;
        $query = Prescricao::find();

        // SE FOR PACIENTE: Vê apenas as suas próprias prescrições
        if ($user->can('paciente')) {
            // Junta com Consulta -> Triagem -> UserProfile para filtrar pelo user_id
            $query->joinWith(['consulta.triagem.userprofile' => function($q) use ($user) {
                $q->where(['user_id' => $user->id]);
            }]);
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
                'medico'       => 'Dr. Teste', // Podes ajustar para buscar o nome do médico real via consulta->medico
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

        // SEGURANÇA: Se for paciente, verificar se a prescrição é dele
        if (Yii::$app->user->can('paciente')) {
            $dono = $prescricao->consulta->triagem->userprofile->user_id ?? null;
            if ($dono != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta prescrição.");
            }
        }

        // A verificação de "propriedade" foi removida porque Pacientes estão bloqueados.
        // Médicos e Enfermeiros podem consultar a prescrição.

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
        // Apenas Médicos e Admins podem prescrever (Enfermeiros não)
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem criar prescrições.");
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

            // MQTT Seguro
            $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
            if ($mqttEnabled && isset(Yii::$app->mqtt)) {
                try {
                    Yii::$app->mqtt->publish(
                        "prescricao/criada/{$prescricao->id}",
                        json_encode([
                            'evento'       => 'prescricao_criada',
                            'prescricao_id'=> $prescricao->id,
                            'consulta_id'  => $prescricao->consulta_id,
                            'hora'         => date('Y-m-d H:i:s'),
                        ])
                    );
                } catch (\Exception $e) {
                    Yii::error("Erro MQTT Prescricao Create: " . $e->getMessage());
                }
            }

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

        // MQTT Seguro
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish(
                    "prescricao/apagada/{$id}",
                    json_encode([
                        'evento'       => 'prescricao_apagada',
                        'prescricao_id'=> $id,
                        'hora'         => date('Y-m-d H:i:s'),
                    ])
                );
            } catch (\Exception $e) {
                Yii::error("Erro MQTT Prescricao Delete: " . $e->getMessage());
            }
        }

        return ['status' => 'success', 'message' => 'Prescrição anulada.'];
    }
}