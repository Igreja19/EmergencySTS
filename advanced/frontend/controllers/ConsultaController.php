<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Consulta;
use common\models\Triagem;

class ConsultaController extends Controller
{
    /**
     * Histórico de consultas do paciente autenticado
     */
    public function actionHistorico()
    {
        $userId = Yii::$app->user->id;

        // Buscar todas as consultas ligadas ao paciente autenticado
        $consultas = Consulta::find()
            ->where(['userprofile_id' => $userId])
            ->orderBy(['data_consulta' => SORT_DESC])
            ->all();

        // KPIs
        $total = Consulta::find()->where(['userprofile_id' => $userId])->count();

        $ultimaConsulta = Consulta::find()
            ->where(['userprofile_id' => $userId])
            ->orderBy(['data_consulta' => SORT_DESC])
            ->one();

        $ultimaVisita = $ultimaConsulta
            ? Yii::$app->formatter->asDatetime($ultimaConsulta->data_consulta, 'php:d/m/Y H:i')
            : '-';

        $prioridadeMaisComum = Consulta::find()
            ->select(['prioridade', 'COUNT(*) AS total'])
            ->where(['userprofile_id' => $userId])
            ->groupBy('prioridade')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->one();
        $prioridadeMaisComum = $prioridadeMaisComum['prioridade'] ?? '-';

        return $this->render('historico', [
            'consultas' => $consultas,
            'total' => $total,
            'ultimaVisita' => $ultimaVisita,
            'prioridadeMaisComum' => $prioridadeMaisComum,
        ]);
    }

    /**
     * Ver detalhes de uma consulta
     */
    public function actionVer($id)
    {
        $consulta = $this->findModel($id);
        return $this->render('ver', [
            'consulta' => $consulta,
            'triagem' => $consulta->triagem ?? null,
        ]);
    }

    /**
     * Marcar consulta como encerrada
     */
    public function actionEncerrar($id)
    {
        $consulta = $this->findModel($id);
        $consulta->estado = 'Encerrada';
        $consulta->data_encerramento = date('Y-m-d H:i:s');
        $consulta->save(false);

        Yii::$app->session->setFlash('success', 'Consulta encerrada com sucesso.');
        return $this->redirect(['historico']);
    }

    /**
     * Função auxiliar para encontrar consulta
     */
    protected function findModel($id)
    {
        if (($model = Consulta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('A consulta solicitada não foi encontrada.');
    }
    public function actionPdf($id)
    {
        $consulta = $this->findModel($id);
        $triagem = $consulta->triagem;

        // Renderizar o conteúdo em HTML
        $html = $this->renderPartial('relatorio', [
            'consulta' => $consulta,
            'triagem' => $triagem,
        ]);

        // Carregar o mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
        ]);

        $mpdf->SetTitle('Relatório da Consulta #' . $consulta->id);
        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooter('<div style="text-align:center;color:#6b7280;font-size:10px;">Página {PAGENO} de {nbpg}</div>');
        $mpdf->Output('Relatorio_Consulta_' . $consulta->id . '.pdf', 'D');
        Yii::$app->end();
    }
}
