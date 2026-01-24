<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Consulta;
use common\models\Triagem;

class ConsultaController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['paciente'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
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
        $prescricao = $consulta->prescricao;
        $medicoNome = $consulta->medico_nome ?? 'Profissional de Saúde';

        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 12,
            'default_font' => 'dejavusans',
        ]);

        $css = file_get_contents(
            Yii::getAlias('@frontend/web/css/consulta/pdf.css')
        );
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

        // HTML
        $html = $this->renderPartial('pdf', [
            'consulta'   => $consulta,
            'prescricao' => $prescricao,
            'medicoNome' => $medicoNome,
        ]);

        if (ob_get_length()) {
            ob_end_clean();
        }

        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        return $mpdf->Output(
            "Consulta_{$consulta->id}.pdf",
            \Mpdf\Output\Destination::DOWNLOAD
        );
    }
}
