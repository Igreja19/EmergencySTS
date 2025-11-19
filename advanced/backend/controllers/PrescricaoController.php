<?php

namespace backend\controllers;

use Yii;
use common\models\Prescricao;
use common\models\PrescricaoSearch;
use common\models\Consulta;
use common\models\Medicamento;
use common\models\Prescricaomedicamento;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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

        // opcional: já traz os medicamentos carregados
        $model->populateRelation('medicamentos', $model->medicamentos);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Cria uma nova prescrição
     */
    public function actionCreate()
    {
        $model = new Prescricao();

        // Lista dropdown de consultas
        $consultas = Consulta::find()
            ->select(['id'])
            ->indexBy('id')
            ->column();

        // Lista dropdown de medicamentos
        $medicamentos = Medicamento::find()
            ->select(['nome'])
            ->indexBy('id')
            ->column();

        if ($model->load(Yii::$app->request->post())) {

            // 🔹 Se a data da prescrição não vier do formulário → usa agora
            if (empty($model->dataprescricao)) {
                $model->dataprescricao = date('Y-m-d H:i:s');
            }

            if ($model->save()) {

                // 🔗 grava associações na tabela pivot prescricaomedicamento
                if (!empty($model->medicamento_ids) && is_array($model->medicamento_ids)) {
                    foreach ($model->medicamento_ids as $medId) {
                        $pm = new Prescricaomedicamento();
                        $pm->prescricao_id  = $model->id;
                        $pm->medicamento_id = $medId;
                        $pm->save(false);
                    }
                }

                Yii::$app->session->setFlash('success', 'Prescrição criada com sucesso!');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            Yii::$app->session->setFlash(
                'error',
                'Erro ao guardar prescrição: ' . json_encode($model->getErrors())
            );
        }

        return $this->render('create', [
            'model'        => $model,
            'consultas'    => $consultas,
            'medicamentos' => $medicamentos,
        ]);
    }

    /**
     * Atualiza uma prescrição existente
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $consultas = Consulta::find()
            ->select(['id'])
            ->indexBy('id')
            ->column();

        $medicamentos = Medicamento::find()
            ->select(['nome'])
            ->indexBy('id')
            ->column();

        // 🔹 Pré-carrega os medicamentos já associados para o dropdown múltiplo
        $model->medicamento_ids = Prescricaomedicamento::find()
            ->select('medicamento_id')
            ->where(['prescricao_id' => $model->id])
            ->column();

        if ($model->load(Yii::$app->request->post())) {

            // 🔹 Se por algum motivo limpar a data, repõe para agora
            if (empty($model->dataprescricao)) {
                $model->dataprescricao = date('Y-m-d H:i:s');
            }

            if ($model->save()) {

                // ❌ remove associações antigas
                Prescricaomedicamento::deleteAll(['prescricao_id' => $model->id]);

                // ✅ recria associações de acordo com o que veio do formulário
                if (!empty($model->medicamento_ids) && is_array($model->medicamento_ids)) {
                    foreach ($model->medicamento_ids as $medId) {
                        $pm = new Prescricaomedicamento();
                        $pm->prescricao_id  = $model->id;
                        $pm->medicamento_id = $medId;
                        $pm->save(false);
                    }
                }

                Yii::$app->session->setFlash('success', 'Prescrição atualizada com sucesso!');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            Yii::$app->session->setFlash(
                'error',
                'Erro ao atualizar: ' . json_encode($model->getErrors())
            );
        }

        return $this->render('update', [
            'model'        => $model,
            'consultas'    => $consultas,
            'medicamentos' => $medicamentos,
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
}
