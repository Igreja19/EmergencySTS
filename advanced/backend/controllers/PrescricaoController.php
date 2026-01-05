<?php

namespace backend\controllers;

use common\models\Notificacao;
use Yii;
use common\models\Prescricao;
use common\models\PrescricaoSearch;
use common\models\Consulta;
use common\models\Medicamento;
use common\models\Prescricaomedicamento;
use yii\filters\AccessControl;
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
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['index','view','create','update','delete','chart-data'],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['error', 'login'],
                        ],
                        [
                            'allow' => true,
                            'roles' => ['admin', 'medico', 'enfermeiro'],
                        ],
                    ],
                    'denyCallback' => function () {
                        return Yii::$app->response->redirect(['/site/login']);
                    },
                ],
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

    public function actionIndex()
    {
        $searchModel = new PrescricaoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $prescricaoMedicamentos = Prescricaomedicamento::find()
            ->where(['prescricao_id' => $model->id])
            ->with('medicamento')
            ->all();

        return $this->render('view', [
            'model' => $model,
            'prescricaoMedicamentos' => $prescricaoMedicamentos,
        ]);
    }

    public function actionCreate($consulta_id = null)
    {
        $model = new Prescricao();

        $consultaId = Yii::$app->request->get('consulta_id');
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

                try {
                    if (Yii::$app->has('mqtt')) {
                        Yii::$app->mqtt->publish(
                            "prescricao/criada/{$model->id}",
                            json_encode([
                                'evento' => 'prescricao_criada_backend',
                                'prescricao_id' => $model->id,
                                'consulta_id' => $model->consulta_id,
                                'hora' => date('Y-m-d H:i:s')
                            ])
                        );
                    }
                } catch (\Exception $e) {
                    Yii::warning("Falha MQTT (Create Prescricao): " . $e->getMessage());
                }

                Yii::$app->session->setFlash('success', 'Prescrição criada com sucesso.');

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

                try {
                    if (Yii::$app->has('mqtt')) {
                        Yii::$app->mqtt->publish(
                            "prescricao/atualizada/{$model->id}",
                            json_encode([
                                'evento' => 'prescricao_atualizada_backend',
                                'prescricao_id' => $model->id,
                                'consulta_id' => $model->consulta_id,
                                'hora' => date('Y-m-d H:i:s')
                            ])
                        );
                    }
                } catch (\Exception $e) {
                    Yii::warning("Falha MQTT (Update Prescricao): " . $e->getMessage());
                }

                Yii::$app->session->setFlash('success', 'Prescrição atualizada com sucesso.');

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

    public function actionDelete($id)
    {
        Prescricaomedicamento::deleteAll(['prescricao_id' => $id]);

        $this->findModel($id)->delete();

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "prescricao/apagada/{$id}",
                    json_encode([
                        'evento' => 'prescricao_apagada_backend',
                        'prescricao_id' => $id,
                        'hora' => date('Y-m-d H:i:s')
                    ])
                );
            }
        } catch (\Exception $e) {
            Yii::warning("Falha MQTT (Delete Prescricao): " . $e->getMessage());
        }

        Yii::$app->session->setFlash('success', 'Prescrição eliminada com sucesso.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Prescricao::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('A prescrição solicitada não existe.');
    }
}