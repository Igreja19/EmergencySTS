<?php

namespace backend\controllers;

use Yii;
use common\models\Consulta;
use common\models\ConsultaSearch;
use common\models\Triagem;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ConsultaController implements the CRUD actions for Consulta model.
 */
class ConsultaController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Consulta models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ConsultaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Consulta model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Consulta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new \common\models\Consulta();

        // 🔹 Buscar triagens que têm pulseira associada
        $triagensDisponiveis = \yii\helpers\ArrayHelper::map(
            \common\models\Triagem::find()
                ->joinWith('pulseira')
                ->where(['not', ['pulseira.id' => null]])
                ->all(),
            'id',
            function ($triagem) {
                // Mostra apenas o código da pulseira
                return 'Pulseira: ' . ($triagem->pulseira->codigo ?? '-');
            }
        );

        // 🔹 Quando o formulário é submetido
        if ($model->load(\Yii::$app->request->post())) {

            $model->prescricao_id = null;

            // Define data de consulta se não estiver preenchida
            if (empty($model->data_consulta)) {
                $model->data_consulta = date('Y-m-d H:i:s');
            }

            // Define estado padrão
            if (empty($model->estado)) {
                $model->estado = 'Aberta';
            }

            // 🔹 Guarda o modelo
            if ($model->save(false)) { // false = ignora validações repetidas
                \Yii::$app->session->setFlash('success', 'Consulta criada com sucesso!');
                return $this->redirect(['index']); // ✅ Redireciona para a listagem
            } else {
                \Yii::$app->session->setFlash('error', 'Erro ao guardar consulta: ' . json_encode($model->getErrors()));
            }
        }

        // 🔹 Renderiza o formulário
        return $this->render('create', [
            'model' => $model,
            'triagensDisponiveis' => $triagensDisponiveis,
        ]);
    }

    // ✅ Novo método AJAX para preencher o paciente automaticamente
    public function actionTriagemInfo($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $triagem = \common\models\Triagem::findOne($id);
        if ($triagem) {
            return [
                'userprofile_id' => $triagem->userprofile_id,
                'user_nome' => $triagem->userprofile->nome ?? '',
            ];
        }

        return [];
    }

    /**
     * Updates an existing Consulta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Consulta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Consulta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Consulta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Consulta::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
