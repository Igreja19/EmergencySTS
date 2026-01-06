<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Consulta;
use common\models\Triagem;

class ConsultaController extends Controller
{

    public function actionHistorico()
    {

        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $user = Yii::$app->user->identity;

        if (!$user->userprofile) {
            Yii::$app->session->setFlash(
                'warning',
                'Ainda não tem um perfil de paciente associado.'
            );
            return $this->redirect(['site/index']);
        }

        $userProfileId = $user->userprofile->id;

        $consultas = Consulta::find()
            ->where([
                'consulta.userprofile_id' => $userProfileId,
                'consulta.estado' => 'Encerrada',
            ])
            ->joinWith(['triagem.pulseira'])
            ->orderBy(['consulta.data_consulta' => SORT_DESC])
            ->all();

        $total = count($consultas);

        $ultimaConsulta = $consultas[0] ?? null;

        $ultimaVisita = $ultimaConsulta
            ? Yii::$app->formatter->asDatetime(
                $ultimaConsulta->data_consulta,
                'php:d/m/Y H:i'
            )
            : '-';

        return $this->render('historico', [
            'consultas'    => $consultas,
            'total'        => $total,
            'ultimaVisita' => $ultimaVisita,
        ]);
    }

    public function actionVer($id)
    {
        $consulta = $this->findModel($id);

        return $this->render('ver', [
            'consulta' => $consulta,
            'triagem' => $consulta->triagem ?? null,
        ]);
    }

    public function actionEncerrar($id)
    {
        $consulta = $this->findModel($id);

        $consulta->estado = 'Encerrada';
        $consulta->data_encerramento = date('Y-m-d H:i:s');
        $consulta->save(false);

        Yii::$app->session->setFlash('success', 'Consulta encerrada com sucesso.');
        return $this->redirect(['historico']);
    }

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
        $triagem  = $consulta->triagem ?? null;

        $html = $this->renderPartial('pdf', [
            'consulta' => $consulta,
            'triagem'  => $triagem,
        ]);

        $cssPath = Yii::getAlias('@frontend/web/css/consulta/pdf.css');
        $css = file_exists($cssPath) ? file_get_contents($cssPath) : '';

        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A4',
            'orientation' => 'P',
            'default_font'=> 'dejavusans',
        ]);

        $mpdf->SetTitle('Relatório da Consulta #' . $consulta->id);

        // CSS
        if (!empty($css)) {
            $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        }

        // HTML
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        // Rodapé
        $mpdf->SetHTMLFooter(
            '<div style="text-align:center;color:#6b7280;font-size:10px;">
            Página {PAGENO} de {nbpg}
        </div>'
        );

        $mpdf->Output(
            'Relatorio_Consulta_' . $consulta->id . '.pdf',
            'D'
        );

        Yii::$app->end();
    }
}
