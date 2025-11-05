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
     * Histórico de consultas do utilizador autenticado
     */
    public function actionHistorico()
    {
        // 🔹 Verifica se o utilizador está autenticado
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $user = Yii::$app->user->identity;

        // 🔹 Verifica se o utilizador tem um perfil associado
        if (!$user->userprofile) {
            Yii::$app->session->setFlash('warning', 'Ainda não tem um perfil de paciente associado.');
            return $this->redirect(['site/index']);
        }

        $userProfileId = $user->userprofile->id;

        // 🔹 Buscar todas as consultas do utilizador autenticado
        $consultas = Consulta::find()
            ->where(['userprofile_id' => $userProfileId])
            ->orderBy(['data_consulta' => SORT_DESC])
            ->all();

        // 🔹 KPIs (estatísticas)
        $total = count($consultas);

        $ultimaConsulta = !empty($consultas) ? $consultas[0] : null;

        $ultimaVisita = $ultimaConsulta
            ? Yii::$app->formatter->asDatetime($ultimaConsulta->data_consulta, 'php:d/m/Y H:i')
            : '-';

        /*$prioridadeMaisComum = Consulta::find()
            ->select(['prioridade', 'COUNT(*) AS total'])
            ->where(['userprofile_id' => $userProfileId])
            ->groupBy('prioridade')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->one();
        $prioridadeMaisComum = $prioridadeMaisComum['prioridade'] ?? '-';*/

        // 🔹 Renderizar view
        return $this->render('historico', [
            'consultas' => $consultas,
            'total' => $total,
            'ultimaVisita' => $ultimaVisita,
            //'prioridadeMaisComum' => $prioridadeMaisComum,
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

    /**
     * Gerar PDF da consulta
     */
    public function actionPdf($id)
    {
        $consulta = $this->findModel($id);
        $triagem = $consulta->triagem ?? null;

        // Renderizar o conteúdo em HTML
        $html = $this->renderPartial('relatorio', [
            'consulta' => $consulta,
            'triagem' => $triagem,
        ]);

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
