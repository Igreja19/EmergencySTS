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

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Formulário clínico registado com sucesso!');
                return $this->redirect(['triagem/index']);
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
