<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\Triagem;
use frontend\models\Pulseira;

class TriagemController extends Controller
{
    public function actionIndex()
    {
        // Apenas exibe a página inicial de triagem (se precisares)
        return $this->render('index');
    }

    public function actionFormulario()
    {
        $model = new Triagem();

        // ✅ Garante que o POST é recebido corretamente
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                Yii::$app->session->setFlash('success', 'Formulário clínico registado com sucesso!');

                // 🔹 Cria automaticamente a pulseira associada
                $pulseira = new Pulseira();
                $pulseira->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
                $pulseira->prioridade = $model->prioridadeatribuida;
                $pulseira->tempoentrada = date('Y-m-d H:i:s');
                $pulseira->triagem_id = $model->id;
                $pulseira->save(false);

                // 🔹 Redireciona para o index de pulseiras
                return $this->redirect(['pulseira/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao gravar os dados. Verifique o formulário.');
            }
        }

        // 🔹 Renderiza o formulário pela primeira vez (ou após erro)
        return $this->render('formulario', [
            'model' => $model,
        ]);
    }
}
