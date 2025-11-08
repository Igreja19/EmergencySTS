<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Triagem;
use common\models\Pulseira;

class TriagemController extends Controller
{
    /**
     * Página inicial da triagem
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Formulário clínico (criação de triagem)
     */
    public function actionFormulario()
    {
        $model = new Triagem();

        // 🔹 Se o utilizador estiver autenticado, associa automaticamente o perfil
        if (!Yii::$app->user->isGuest) {
            $model->userprofile_id = Yii::$app->user->identity->userprofile->id ?? null;
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->datatriagem = date('Y-m-d H:i:s');

            // 🔹 1️⃣ Criar automaticamente a pulseira
            $pulseira = new Pulseira();
            $pulseira->codigo = strtoupper(substr(md5(uniqid()), 0, 8)); // código único
            $pulseira->prioridade = 'Pendente'; // cor inicial
            $pulseira->tempoentrada = date('Y-m-d H:i:s');
            $pulseira->status = 'Aguardando';
            $pulseira->userprofile_id = $model->userprofile_id;

            if ($pulseira->save(false)) {
                $model->pulseira_id = $pulseira->id;
            }

            // 🔹 2️⃣ Guardar triagem
            if ($model->save(false)) {
                // 🔹 3️⃣ Criar prescrição placeholder
                $prescricao = new \common\models\Prescricao();
                $prescricao->observacoes = 'Prescrição inicial automática';
                $prescricao->dataprescricao = date('Y-m-d H:i:s');
                $prescricao->save(false);

                // 🔹 4️⃣ Criar consulta associada
                $consulta = new \common\models\Consulta();
                $consulta->data_consulta = date('Y-m-d H:i:s');
                $consulta->estado = 'Aberta';
                $consulta->observacoes = 'Consulta gerada automaticamente a partir da triagem.';
                $consulta->userprofile_id = $model->userprofile_id;
                $consulta->triagem_id = $model->id;
                $consulta->prescricao_id = $prescricao->id;
                $consulta->save(false);

                // 🔹 5️⃣ Atualizar prescrição com ID da consulta
                $prescricao->consulta_id = $consulta->id;
                $prescricao->save(false);

                Yii::$app->session->setFlash('success', 'Formulário clínico, pulseira e consulta criados com sucesso!');
                return $this->redirect(['pulseira/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao guardar os dados da triagem.');
            }
        }

        // 🔹 Renderização normal do formulário
        return $this->render('formulario', [
            'model' => $model,
        ]);
    }

}
