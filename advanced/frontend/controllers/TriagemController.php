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

            // 🔹 Cria automaticamente a pulseira (sem cor definida)
            $pulseira = new Pulseira();
            $pulseira->codigo = strtoupper(substr(md5(uniqid()), 0, 8)); // código único
            $pulseira->prioridade = 'Pendente'; // cor ainda não atribuída
            $pulseira->tempoentrada = date('Y-m-d H:i:s');
            $pulseira->status = 'Em espera';
            $pulseira->userprofile_id = $model->userprofile_id;

            // 🔹 Guarda a pulseira e associa à triagem
            if ($pulseira->save(false)) {
                $model->pulseira_id = $pulseira->id;
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Formulário clínico registado e pulseira criada com sucesso!');
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
