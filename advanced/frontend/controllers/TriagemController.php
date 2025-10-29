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

            // 🔹 Cria primeiro a pulseira (para evitar erro de FK)
            $pulseira = new Pulseira();
            $pulseira->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
            $pulseira->prioridade = Yii::$app->request->post('Pulseira')['prioridade'] ?? 'Verde';
            $pulseira->tempoentrada = date('Y-m-d H:i:s');
            $pulseira->status = 'Aguardando';
            $pulseira->userprofile_id = $model->userprofile_id;

            if ($pulseira->save(false)) {
                // 🔹 Agora que a pulseira existe, completa e guarda a triagem
                $model->pulseira_id = $pulseira->id;
                $model->datatriagem = date('Y-m-d H:i:s');

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Formulário clínico registado e pulseira criada com sucesso!');
                    // 🔹 Redireciona para triagem/index em vez de pulseira/index
                    return $this->redirect(['triagem/index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Erro ao guardar os dados da triagem.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao criar a pulseira.');
            }
        }

        // 🔹 Renderização normal do formulário
        return $this->render('formulario', [
            'model' => $model,
        ]);
    }
}
