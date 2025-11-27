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
                    'only' => ['index','view','create','update','delete','chart-data', 'historico'],
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

                // 🔧 VerbFilter
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

        $triagensDisponiveis = ArrayHelper::map(
            Triagem::find()
                ->joinWith('pulseira')
                ->where(['not', ['pulseira.prioridade' => 'Pendente']])
                ->andWhere(['not', ['pulseira.prioridade' => null]])
                ->andWhere(['pulseira.status' => 'Em espera'])
                ->groupBy('pulseira.id') // <-- Para evitar repetidos
                ->all(),
            'id',
            function($t) {
                $cor = $t->pulseira->prioridade ?? '—';
                $codigo = $t->pulseira->codigo ?? 'Sem código';
                return "Pulseira: {$cor} ({$codigo})";
            }
        );

        if ($model->load(Yii::$app->request->post())) {

            // Dados automáticos
            $model->data_consulta = date('Y-m-d H:i:s');
            $model->estado = Consulta::ESTADO_EM_CURSO;
            $model->data_encerramento = null;

            if ($model->save(false)) {

                // Atualizar pulseira para "Em atendimento"
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;
                    $pulseira->status = "Em atendimento";
                    $pulseira->save(false);
                }

                Yii::$app->session->setFlash('success', 'Consulta criada com sucesso!');
                return $this->redirect(['update', 'id' => $model->id]);
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

        // 🔹 Triagens com pulseira — faltava no update!
        $triagensDisponiveis = ArrayHelper::map(
            Triagem::find()
                ->joinWith('pulseira')
                ->where(['not', ['pulseira.id' => null]])
                ->all(),
            'id',
            fn($t) => 'Pulseira: ' . ($t->pulseira->codigo ?? '—')
        );

        if ($model->load(Yii::$app->request->post())) {

            // Se volta para "Em curso"
            if ($model->estado === Consulta::ESTADO_EM_CURSO) {
                $model->data_encerramento = null;
            }

            // Se encerra e data ainda não existe
            if ($model->estado === Consulta::ESTADO_ENCERRADA && empty($model->data_encerramento)) {
                $model->data_encerramento = date('Y-m-d H:i:s');
            }

            if ($model->save(false)) {

                // Atualizar estado da pulseira
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;

                    $pulseira->status =
                        $model->estado === Consulta::ESTADO_ENCERRADA
                            ? "Atendido"
                            : "Em atendimento";

                    $pulseira->save(false);
                }

                Yii::$app->session->setFlash('success', 'Consulta atualizada com sucesso!');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'triagensDisponiveis' => $triagensDisponiveis, // 🔥 FIX AQUI
        ]);
    }

    public function actionHistorico()
    {
        // Buscar todos os médicos via RBAC
        $medicoAssignments = Yii::$app->authManager->getUserIdsByRole('medico');

        // Perfis dos médicos
        $medicos = \common\models\UserProfile::find()
            ->where(['user_id' => $medicoAssignments])
            ->all();

        // Consultas encerradas
        $consultas = \common\models\Consulta::find()
            ->where(['estado' => 'Encerrada'])
            ->orderBy(['data_encerramento' => SORT_DESC])
            ->all();

        return $this->render('historico', [
            'medicos' => $medicos,
            'consultas' => $consultas,
        ]);
    }

    public function actionEncerrar($id)
    {
        $model = $this->findModel($id);
        $model->estado = 'Encerrada';
        $model->data_encerramento = date('Y-m-d H:i:s');
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Consulta encerrada com sucesso!');
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Consulta eliminada com sucesso.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Consulta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('A consulta solicitada não existe.');
    }
}
