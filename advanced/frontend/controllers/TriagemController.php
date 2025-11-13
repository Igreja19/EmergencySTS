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

        if (!Yii::$app->user->isGuest) {
            $model->userprofile_id = Yii::$app->user->identity->userprofile->id ?? null;
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            $model->datatriagem = date('Y-m-d H:i:s');

            // 1️⃣ Criar pulseira
            $pulseira = new Pulseira();
            $pulseira->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
            $pulseira->prioridade = 'Pendente';
            $pulseira->tempoentrada = date('Y-m-d H:i:s');
            $pulseira->status = 'Em espera';
            $pulseira->userprofile_id = $model->userprofile_id;
            $pulseira->save(false);

            $model->pulseira_id = $pulseira->id;

            // 2️⃣ Guardar triagem
            if ($model->save(false)) {

                // 3️⃣ Criar consulta em primeiro lugar
                $consulta = new \common\models\Consulta();
                $consulta->data_consulta = date('Y-m-d H:i:s');
                $consulta->estado = 'Aberta';
                $consulta->observacoes = 'Consulta gerada automaticamente a partir da triagem.';
                $consulta->userprofile_id = $model->userprofile_id;
                $consulta->triagem_id = $model->id;
                $consulta->save(false);

                // 4️⃣ Criar prescrição associada à consulta
                $prescricao = new \common\models\Prescricao();
                $prescricao->consulta_id = $consulta->id;  // OBRIGATÓRIO
                $prescricao->observacoes = 'Prescrição inicial automática';
                $prescricao->dataprescricao = date('Y-m-d H:i:s');
                $prescricao->save(false);

                Yii::$app->session->setFlash('success', 'Formulário clínico, pulseira, consulta e prescrição criados com sucesso!');
                return $this->redirect(['pulseira/index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao guardar os dados da triagem.');
        }

        return $this->render('formulario', [
            'model' => $model,
        ]);
    }

}
