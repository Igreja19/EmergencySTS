<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\Pulseira;

class PulseiraController extends Controller
{
    public function actionIndex()
    {
        // Obtém a última pulseira criada
        $pulseira = Pulseira::find()->orderBy(['id' => SORT_DESC])->one();

        // Se não houver nenhuma, apenas mostra mensagem
        if (!$pulseira) {
            Yii::$app->session->setFlash('error', 'Nenhuma pulseira encontrada.');
            return $this->render('index', [
                'pulseira' => null,
                'pacienteNome' => null,
            ]);
        }

        // Busca o nome do paciente associado (se existir o modelo Paciente)
        $pacienteNome = 'Desconhecido';
        if (class_exists('\frontend\models\Paciente')) {
            $paciente = \frontend\models\Paciente::findOne($pulseira->paciente_id);
            if ($paciente) {
                $pacienteNome = $paciente->nome;
            }
        }

        // Renderiza a página
        return $this->render('index', [
            'pulseira' => $pulseira,
            'pacienteNome' => $pacienteNome,
        ]);
    }
}
