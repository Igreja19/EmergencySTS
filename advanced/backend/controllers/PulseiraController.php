<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\Pulseira;
use common\models\PulseiraSearch;
use common\models\Triagem;
use common\models\UserProfile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

class PulseiraController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'only' => ['index','view','create','update','delete'],
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['admin', 'medico', 'enfermeiro'],
                        ],
                    ],
                    'denyCallback' => fn() => Yii::$app->response->redirect(['/site/login']),
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new PulseiraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Pulseira();
        $triagem = new Triagem();

        $pacientes = \yii\helpers\ArrayHelper::map(
            UserProfile::find()->all(),
            'id',
            'nome'
        );

        if (Yii::$app->request->isPost) {

            if ($model->load(Yii::$app->request->post())) {

                $model->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
                $model->prioridade = 'Pendente';
                $model->tempoentrada = date('Y-m-d H:i:s');
                $model->status = 'Em espera';

                if ($model->save(false)) {

                    Notificacao::enviar(
                        $model->userprofile_id,
                        "Pulseira atribuída",
                        "Foi criada uma nova pulseira pendente.",
                        "Consulta"
                    );

                    $triagem->userprofile_id = $model->userprofile_id;
                    $triagem->pulseira_id = $model->id;
                    $triagem->datatriagem = date('Y-m-d H:i:s');
                    $triagem->motivoconsulta = '';
                    $triagem->queixaprincipal = '';
                    $triagem->descricaosintomas = '';
                    $triagem->iniciosintomas = null;
                    $triagem->intensidadedor = 0;
                    $triagem->alergias = '';
                    $triagem->medicacao = '';
                    $triagem->save(false);

                    Yii::$app->mqtt->publish(
                        "pulseira/criada/{$model->id}",
                        json_encode([
                            'evento' => 'pulseira_criada_backend',
                            'pulseira_id' => $model->id,
                            'userprofile_id' => $model->userprofile_id,
                            'hora' => date('Y-m-d H:i:s')
                        ])
                    );

                    Yii::$app->session->setFlash('success', 'Pulseira pendente criada com triagem associada.');
                    return $this->redirect(['index']);
                }

                Yii::$app->session->setFlash('error', 'Erro ao criar a pulseira.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'pacientes' => $pacientes,
            'triagem' => $triagem,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $oldPriority = $model->prioridade;

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {

            $newPriority = $model->prioridade;

            if ($newPriority !== $oldPriority) {

                Notificacao::enviar(
                    $model->userprofile_id,
                    "Prioridade atualizada",
                    "A pulseira foi atualizada para prioridade " . $newPriority . ".",
                    "Geral"
                );

                if (in_array($newPriority, ['Vermelho', 'Laranja'])) {
                    Notificacao::enviar(
                        $model->userprofile_id,
                        "Prioridade crítica: " . $newPriority,
                        "O paciente encontra-se agora em prioridade crítica.",
                        "Prioridade"
                    );
                }
            }

            Yii::$app->mqtt->publish(
                "pulseira/atualizada/{$model->id}",
                json_encode([
                    'evento' => 'pulseira_atualizada_backend',
                    'pulseira_id' => $model->id,
                    'prioridade' => $model->prioridade,
                    'status' => $model->status,
                    'hora' => date('Y-m-d H:i:s'),
                ])
            );

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $pulseira = $this->findModel($id);

        $triagem = Triagem::find()
            ->where(['pulseira_id' => $pulseira->id])
            ->one();

        if ($triagem) {

            $consultas = \common\models\Consulta::find()
                ->where(['triagem_id' => $triagem->id])
                ->all();

            foreach ($consultas as $consulta) {

                foreach ($consulta->prescricoes as $p) {
                    $p->delete();
                }

                $consulta->delete();
            }

            $triagem->delete();
        }

        $pulseira->delete();

        Yii::$app->mqtt->publish(
            "pulseira/apagada/{$id}",
            json_encode([
                'evento' => 'pulseira_apagada_backend',
                'pulseira_id' => $id,
                'hora' => date('Y-m-d H:i:s'),
            ])
        );

        Yii::$app->session->setFlash('success', 'Pulseira e todos os dados associados foram eliminados.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Pulseira::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException("A pulseira não existe.");
    }
}
