<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\Prescricaomedicamento;
use common\models\Pulseira;
use common\models\Triagem;
use common\models\TriagemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\web\Response;

class TriagemController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'only' => ['index','view','create','update','delete','chart-data'],
                    'rules' => [
                        ['allow' => true, 'actions' => ['error', 'login']],
                        ['allow' => true, 'roles' => ['admin', 'medico', 'enfermeiro']],
                    ],
                    'denyCallback' => fn() => Yii::$app->response->redirect(['/site/login']),
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
        $searchModel = new TriagemSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $model = new Triagem();

        if ($this->request->isPost && $model->load($this->request->post())) {

            $pulseiraExistente = Pulseira::find()
                ->where(['userprofile_id' => $model->userprofile_id])
                ->andWhere(['in', 'prioridade', ['Vermelho','Laranja','Amarelo','Verde','Azul']])
                ->one();

            if ($pulseiraExistente) {
                Yii::$app->session->setFlash('danger', 'Este paciente já tem pulseira atribuída.');
                return $this->redirect(['index']);
            }

            $triagemExistente = Triagem::find()
                ->where(['userprofile_id' => $model->userprofile_id])
                ->andWhere(['pulseira_id' => null])
                ->one();

            if ($triagemExistente) {
                Yii::$app->session->setFlash('danger', 'Este paciente já tem triagem pendente.');
                return $this->redirect(['index']);
            }

            if ($model->save(false)) {

                if (!empty($model->prioridade_pulseira)) {
                    $pulseira = Pulseira::findOne($model->pulseira_id);
                    if ($pulseira) {
                        $pulseira->prioridade = $model->prioridade_pulseira;
                        $pulseira->status = "Em espera";
                        $pulseira->save(false);
                    }
                }

                Yii::$app->mqtt->publish(
                    "triagem/criada/{$model->id}",
                    json_encode([
                        'evento' => 'triagem_criada_backend',
                        'triagem_id' => $model->id,
                        'userprofile_id' => $model->userprofile_id,
                        'hora' => date('Y-m-d H:i:s'),
                    ])
                );

                return $this->redirect(['index']);
            }
        }

        $model->loadDefaultValues();

        if ($model->iniciosintomas) {
            $model->iniciosintomas = date('Y-m-d\TH:i', strtotime($model->iniciosintomas));
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionPulseirasPorPaciente($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $pulseiras = Pulseira::find()
            ->where([
                'userprofile_id' => $id,
                'prioridade' => 'Pendente',
            ])
            ->orderBy(['tempoentrada' => SORT_DESC])
            ->all();

        $result = [];

        foreach ($pulseiras as $p) {
            $result[] = [
                'id' => $p->id,
                'codigo' => $p->codigo
                    . ' — ' . $p->prioridade
                    . ' — ' . date('d/m/Y H:i', strtotime($p->tempoentrada)),
            ];
        }

        return $result;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {

            if (!empty($model->prioridade_pulseira)) {
                $pulseira = Pulseira::findOne($model->pulseira_id);
                if ($pulseira) {
                    $pulseira->prioridade = $model->prioridade_pulseira;
                    $pulseira->status = "Em espera";
                    $pulseira->save(false);
                }
            }

            if ($model->save(false)) {

                Yii::$app->mqtt->publish(
                    "triagem/atualizada/{$model->id}",
                    json_encode([
                        'evento' => 'triagem_atualizada_backend',
                        'triagem_id' => $model->id,
                        'hora' => date('Y-m-d H:i:s'),
                    ])
                );

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $triagem = $this->findModel($id);

        $consultas = \common\models\Consulta::find()
            ->where(['triagem_id' => $triagem->id])
            ->all();

        foreach ($consultas as $consulta) {
            foreach ($consulta->prescricoes as $prescricao) {
                Prescricaomedicamento::deleteAll(['prescricao_id' => $prescricao->id]);
                $prescricao->delete();
            }
            $consulta->delete();
        }

        $pulseira = $triagem->pulseira;
        $triagem->delete();

        if ($pulseira) {
            $pulseira->delete();
        }

        Yii::$app->mqtt->publish(
            "triagem/apagada/{$id}",
            json_encode([
                'evento' => 'triagem_apagada_backend',
                'triagem_id' => $id,
                'hora' => date('Y-m-d H:i:s'),
            ])
        );

        Yii::$app->session->setFlash('success', 'Triagem e dados associados eliminados.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Triagem::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('A triagem solicitada não existe.');
    }

    public function actionChartData($start = null, $end = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Triagem::find();

        if ($start && $end) {
            $query->andWhere(['between', 'datatriagem', $start . ' 00:00:00', $end . ' 23:59:59']);
        }

        $triagens = $query->orderBy('datatriagem')->all();

        $labels = [];
        $counts = [];

        foreach ($triagens as $t) {
            $date = date('d-m-Y', strtotime($t->datatriagem));
            if (!isset($counts[$date])) {
                $counts[$date] = 0;
            }
            $counts[$date]++;
        }

        return [
            'labels' => array_keys($counts),
            'data'   => array_values($counts)
        ];
    }
    public function actionDadosPulseira($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $pulseira = Pulseira::find()
            ->with('triagem')
            ->where(['id' => $id])
            ->one();

        if (!$pulseira || !$pulseira->triagem) {
            return [];
        }

        $t = $pulseira->triagem;

        return [
            'prioridade'        => $pulseira->prioridade,
            'motivoconsulta'    => $t->motivoconsulta,
            'queixaprincipal'   => $t->queixaprincipal,
            'descricaosintomas' => $t->descricaosintomas,
            'iniciosintomas'    => $t->iniciosintomas,
            'intensidadedor'    => $t->intensidadedor,
            'alergias'          => $t->alergias,
            'medicacao'         => $t->medicacao,
        ];
    }

}
