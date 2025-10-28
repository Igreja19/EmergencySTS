<?php
namespace frontend\controllers;

use common\models\Pulseira;
use common\models\Triagem;
use Yii;
use yii\web\Controller;

class TriagemController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionFormulario()
    {
        $model = new Triagem();

        if (!Yii::$app->user->isGuest) {
            $model->nomecompleto = Yii::$app->user->identity->username;
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                Yii::$app->session->setFlash('success', 'Formulário clínico registado com sucesso!');

                // 🔹 Cria automaticamente a pulseira associada ao paciente
                $pulseira = new Pulseira();
                $pulseira->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
                $pulseira->prioridade = $model->prioridadeatribuida;
                $pulseira->tempoentrada = date('Y-m-d H:i:s');
                $pulseira->triagem_id = $model->id;
                $pulseira->paciente_id = $model->paciente_id; // ✅ associação direta ao paciente
                $pulseira->status = 'Aguardando';
                $pulseira->save(false);

                // 🔹 Redireciona para o painel da pulseira
                return $this->redirect(['pulseira/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao gravar os dados. Verifique o formulário.');
            }
        }

        return $this->render('formulario', [
            'model' => $model,
        ]);
    }
}
