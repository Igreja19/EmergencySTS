<?php

namespace backend\controllers;

use common\models\Notificacao;
use Yii;
use common\models\Prescricao;
use common\models\PrescricaoSearch;
use common\models\Consulta;
use common\models\Medicamento;
use common\models\Prescricaomedicamento;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\helpers\ModelHelper;
use yii\base\Model;

class PrescricaoController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [

                // 🔒 CONTROLO DE ACESSO (protege rotas)
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'only' => ['index','view','create','update','delete','chart-data'], // rotas protegidas
                    'rules' => [

                        // 👉 login e error apenas no SiteController (ignora aqui)
                        [
                            'allow' => true,
                            'actions' => ['error', 'login'],
                        ],

                        // 👉 permitir apenas ADMIN, MÉDICO e ENFERMEIRO
                        [
                            'allow' => true,
                            'roles' => ['admin', 'medico', 'enfermeiro'],
                        ],
                    ],
                    'denyCallback' => function () {
                        return Yii::$app->response->redirect(['/site/login']);
                    },
                ],

                // 🔧 VerbFilter já existia, continua igual
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'chart-data' => ['GET'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lista todas as prescrições
     */
    public function actionIndex()
    {
        $searchModel = new PrescricaoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Mostra uma prescrição específica
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Carrega os dados pivot (medicamentos + posologia)
        $prescricaoMedicamentos = Prescricaomedicamento::find()
            ->where(['prescricao_id' => $model->id])
            ->with('medicamento') // traz o nome do medicamento
            ->all();

        return $this->render('view', [
            'model' => $model,
            'prescricaoMedicamentos' => $prescricaoMedicamentos,
        ]);
    }


    /**
     * Cria uma nova prescrição
     */
    public function actionCreate($consulta_id = null)
    {
        $model = new Prescricao();


        // Recebe consulta_id da URL (se existir)
        $consultaId = Yii::$app->request->get('consulta_id');

        // Preenche o campo automaticamente
        if ($consultaId) {
            $model->consulta_id = $consultaId;
        }

        $consultas = Consulta::find()
            ->where(['estado' => Consulta::ESTADO_EM_CURSO])
            ->select(['id'])
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->column();

        $medicamentosDropdown = Medicamento::find()->select(['nome'])->indexBy('id')->column();

        $prescricaoMedicamentos = [new Prescricaomedicamento];

        if ($model->load(Yii::$app->request->post())) {

            $prescricaoMedicamentos = ModelHelper::createMultiple(Prescricaomedicamento::class);
            ModelHelper::loadMultiple($prescricaoMedicamentos, Yii::$app->request->post());

            if ($model->save(false)) {

                foreach ($prescricaoMedicamentos as $pm) {
                    $pm->prescricao_id = $model->id;
                    $pm->save(false);
                }

                // 🔥 NOTIFICAÇÃO AO PACIENTE
                if ($model->consulta && $model->consulta->triagem) {

                    $userId = $model->consulta->triagem->userprofile_id;
                    $nomePaciente = $model->consulta->triagem->userprofile->nome;

                    Notificacao::enviar(
                        $userId,
                        "Nova prescrição",
                        "Foi emitida uma nova prescrição para o paciente {$nomePaciente}.",
                        "Consulta"
                    );
                }

                Yii::$app->session->setFlash('success', 'Prescrição criada com sucesso!');

                return $this->redirect([
                    'consulta/update',
                    'id' => $model->consulta_id
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'consultas' => $consultas,
            'medicamentosDropdown' => $medicamentosDropdown,
            'prescricaoMedicamentos' => $prescricaoMedicamentos,
        ]);
    }




    /**
     * Atualiza uma prescrição existente
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $consultas = Consulta::find()
            ->where(['estado' => Consulta::ESTADO_EM_CURSO])
            ->select(['id'])
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->column();

        $medicamentos = Medicamento::find()->select(['nome'])->indexBy('id')->column();

        $prescricaoMedicamentos = Prescricaomedicamento::find()
            ->where(['prescricao_id' => $model->id])
            ->all();

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = array_column($prescricaoMedicamentos, 'id');

            $prescricaoMedicamentos = ModelHelper::createMultiple(
                Prescricaomedicamento::class,
                $prescricaoMedicamentos
            );

            ModelHelper::loadMultiple($prescricaoMedicamentos, Yii::$app->request->post());

            $newIDs = array_filter(array_column($prescricaoMedicamentos, 'id'));
            $deletedIDs = array_diff($oldIDs, $newIDs);

            if (!empty($deletedIDs)) {
                Prescricaomedicamento::deleteAll(['id' => $deletedIDs]);
            }

            if ($model->save(false)) {

                foreach ($prescricaoMedicamentos as $pm) {
                    $pm->prescricao_id = $model->id;
                    $pm->save(false);
                }

                // 🔥 NOTIFICAÇÃO DE ATUALIZAÇÃO
                if ($model->consulta && $model->consulta->triagem) {

                    $userId = $model->consulta->triagem->userprofile_id;
                    $nomePaciente = $model->consulta->triagem->userprofile->nome;

                    Notificacao::enviar(
                        $userId,
                        "Prescrição atualizada",
                        "A prescrição do paciente {$nomePaciente} foi atualizada.",
                        "Consulta"
                    );
                }

                Yii::$app->session->setFlash('success', 'Prescrição atualizada com sucesso!');

                return $this->redirect([
                    'consulta/update',
                    'id' => $model->consulta_id
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'consultas' => $consultas,
            'medicamentosDropdown' => $medicamentos,
            'prescricaoMedicamentos' => $prescricaoMedicamentos,
        ]);
    }


    /**
     * Apaga uma prescrição
     */
    public function actionDelete($id)
    {
        // primeiro apaga as associações na tabela pivot
        Prescricaomedicamento::deleteAll(['prescricao_id' => $id]);

        // depois apaga a prescrição
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', 'Prescrição eliminada com sucesso.');
        return $this->redirect(['index']);
    }

    /**
     * Procura um modelo Prescricao ou lança erro 404
     */
    protected function findModel($id)
    {
        if (($model = Prescricao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('A prescrição solicitada não existe.');
    }
    public function actionPdf($id)
    {
        $model = $this->findModel($id);
        $consulta = $model->consulta;

        // 🔒 BLOQUEIO: só permite PDF se a consulta estiver ENCERRADA
        if (!$consulta || $consulta->estado !== 'Encerrada') {
            Yii::$app->session->setFlash(
                'error',
                'Só é possível gerar o PDF após a consulta estar encerrada.'
            );

            return $this->redirect(['view', 'id' => $model->id]);
        }

        // Nome do médico responsável
        $medicoNome = $consulta->userprofile->nomecompleto
            ?? $consulta->userprofile->username
            ?? 'Profissional de Saúde';

        // Configuração do mPDF
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 12,
            'default_font' => 'dejavusans'
        ]);

        // Renderização da view PDF
        $html = $this->renderPartial('pdf', [
            'model'      => $model,
            'consulta'   => $consulta,
            'medicoNome' => $medicoNome
        ]);

        $mpdf->WriteHTML($html);

        // Download do ficheiro
        return $mpdf->Output("Prescricao_{$model->id}.pdf", \Mpdf\Output\Destination::DOWNLOAD);
    }
}
