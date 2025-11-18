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
                    'class' => \yii\filters\VerbFilter::class,
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
     * 🚀 CRIAR CONSULTA
     * =============================================
     */
    public function actionCreate()
    {
        $model = new Consulta();

        // 🔹 Triagens que já têm pulseira atribuída (pacientes válidos p/ consulta)
        $triagensDisponiveis = ArrayHelper::map(
            Triagem::find()
                ->joinWith('pulseira')
                ->where(['not', ['pulseira.id' => null]])
                ->all(),
            'id',
            fn($t) => 'Pulseira: ' . ($t->pulseira->codigo ?? '—')
        );

        if ($model->load(Yii::$app->request->post())) {

            // 🔥 data atual da consulta
            $model->data_consulta = date('Y-m-d H:i:s');

            // 🔥 estado inicial
            $model->estado = Consulta::ESTADO_EM_CURSO;

            // 🔥 data de encerramento não pode existir ao criar
            $model->data_encerramento = null;

            if ($model->save(false)) {

                /**
                 * ⭐ AO CRIAR CONSULTA -> PULSEIRA FICA "EM ATENDIMENTO"
                 */
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;
                    $pulseira->status = "Em atendimento";
                    $pulseira->save(false);
                }

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
     * ✏ EDITAR CONSULTA
     * =============================================
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $estadoAntigo = $model->estado;

        if ($model->load(Yii::$app->request->post())) {

            // 🔹 Se voltar para "Em curso", limpar data encerramento
            if ($model->estado === Consulta::ESTADO_EM_CURSO) {
                $model->data_encerramento = null;
            }

            // 🔹 Se for encerrada e ainda sem data → gerar
            if ($model->estado === Consulta::ESTADO_ENCERRADA && empty($model->data_encerramento)) {
                $model->data_encerramento = date('Y-m-d H:i:s');
            }

            if ($model->save(false)) {

                /**
                 * ⭐ ATUALIZAÇÃO DO ESTADO DA PULSEIRA
                 * -----------------------------------
                 * Se consulta muda para "Encerrada" → pulseira vira "Atendido"
                 */
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;

                    if ($model->estado === Consulta::ESTADO_ENCERRADA) {
                        $pulseira->status = "Atendido";
                    } else {
                        $pulseira->status = "Em atendimento";
                    }

                    $pulseira->save(false);
                }

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
     * ❌ APAGAR CONSULTA
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
