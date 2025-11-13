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
        $podeCriarTriagem = true;

        if (!Yii::$app->user->isGuest) {
            $userProfileId = Yii::$app->user->identity->userprofile->id ?? null;

            if ($userProfileId) {
                $pulseiraAtiva = Pulseira::find()
                    ->where(['userprofile_id' => $userProfileId])
                    ->andWhere(['in', 'status', ['Pendente', 'Em Atendimento']])
                    ->exists(); // devolve true/false, não carrega o modelo inteiro

                $podeCriarTriagem = !$pulseiraAtiva;
            }
        }

        return $this->render('index', [
            'podeCriarTriagem' => $podeCriarTriagem,
        ]);
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

        // Verifica se o utilizador já tem uma pulseira com status "Aguardando" ou "Em Atendimento"
        $pulseiraAtiva = Pulseira::find()
            ->where(['userprofile_id' => $model->userprofile_id])
            ->andWhere(['in', 'status', ['Pendente', 'Em Atendimento']])
            ->one();

        if ($pulseiraAtiva) {
            Yii::$app->session->setFlash('warning', 'Já tem uma pulseira ativa e não pode criar outro formulário até esta ser concluída.');
            return $this->redirect(['site/index']);
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->datatriagem = date('Y-m-d H:i:s');

            // 🔹 1️⃣ Criar automaticamente a pulseira
            $pulseira = new Pulseira();
            $pulseira->codigo = strtoupper(substr(md5(uniqid()), 0, 8)); // código único
            $pulseira->prioridade = 'Pendente'; // cor inicial
            $pulseira->tempoentrada = date('Y-m-d H:i:s');
            $pulseira->status = 'Em espera';
            $pulseira->userprofile_id = $model->userprofile_id;

            if ($pulseira->save(false)) {
                $model->pulseira_id = $pulseira->id;
            }

            // 🔹 2️⃣ Guardar triagem
            if ($model->save(false)) {

                Yii::$app->session->setFlash('success', 'Formulário clínico criado com sucesso!');
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
