<?php

namespace backend\controllers;

use Yii;
use common\models\Consulta;
use common\models\ConsultaSearch;
use common\models\Triagem;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ConsultaController extends Controller
{
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

    public function actionIndex()
    {
        $searchModel = new ConsultaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'   => $searchModel,
            'dataProvider'  => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * =============================================
     *      🚀 CRIAR CONSULTA
     * =============================================
     */
    public function actionCreate()
    {
        $model = new Consulta();

        // 🔹 Obter triagens com pulseira associada
        $triagensDisponiveis = ArrayHelper::map(
            Triagem::find()
                ->joinWith('pulseira')
                ->where(['not', ['pulseira.id' => null]])
                ->all(),
            'id',
            function ($t) {
                return 'Pulseira: ' . ($t->pulseira->codigo ?? '-');
            }
        );

        if ($model->load(Yii::$app->request->post())) {

            // 🔥 Atribui automaticamente a data e hora atuais
            $model->data_consulta = date('Y-m-d H:i:s');

            // 🔥 Estado inicial SEMPRE “Em curso”
            $model->estado = Consulta::ESTADO_EM_CURSO;

            // 🔥 Data de encerramento obrigatoriamente nula ao criar
            $model->data_encerramento = null;

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Consulta criada com sucesso!');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao guardar consulta.');
        }

        return $this->render('create', [
            'model' => $model,
            'triagensDisponiveis' => $triagensDisponiveis,
        ]);
    }

    /**
     * AJAX — devolve info da triagem
     */
    public function actionTriagemInfo($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $triagem = Triagem::findOne($id);

        if ($triagem) {
            return [
                'userprofile_id' => $triagem->userprofile_id,
                'user_nome'      => $triagem->userprofile->nome ?? '',
            ];
        }

        return [];
    }

    /**
     * =============================================
     *      ✏ EDITAR CONSULTA
     * =============================================
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            // 🔹 Se estado voltar a "Em curso", remover data de encerramento
            if ($model->estado === Consulta::ESTADO_EM_CURSO) {
                $model->data_encerramento = null;
            }

            // 🔹 Se marcada como encerrada e sem data → gerar timestamp
            if ($model->estado === Consulta::ESTADO_ENCERRADA && empty($model->data_encerramento)) {
                $model->data_encerramento = date('Y-m-d H:i:s');
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Consulta atualizada com sucesso!');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * =============================================
     *      ❌ APAGAR CONSULTA
     * =============================================
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Consulta eliminada com sucesso.');
        return $this->redirect(['index']);
    }

    /**
     * Encontrar consulta
     */
    protected function findModel($id)
    {
        if (($model = Consulta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('A consulta solicitada não existe.');
    }
}
