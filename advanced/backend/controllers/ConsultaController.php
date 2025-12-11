<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\Prescricaomedicamento;
use common\models\UserProfile;
use Yii;
use common\models\Consulta;
use common\models\ConsultaSearch;
use common\models\Triagem;
use yii\data\ActiveDataProvider;
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

                // CONTROLO DE ACESSO
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'only' => ['index','view','create','update','delete','chart-data', 'historico', 'encerrar'],
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

                // Métodos permitidos
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

     //CRIAR CONSULTA + MQTT
    public function actionCreate()
    {
        $model = new Consulta();

        $triagensDisponiveis = ArrayHelper::map(
            Triagem::find()
                ->joinWith('pulseira')
                ->where(['not', ['pulseira.prioridade' => 'Pendente']])
                ->andWhere(['not', ['pulseira.prioridade' => null]])
                ->andWhere(['pulseira.status' => 'Em espera'])
                ->groupBy('pulseira.id')
                ->all(),
            'id',
            fn($t) => "Pulseira: {$t->pulseira->prioridade} ({$t->pulseira->codigo})"
        );

        if ($model->load(Yii::$app->request->post())) {

            $model->data_consulta = date('Y-m-d H:i:s');
            $model->estado = Consulta::ESTADO_EM_CURSO;
            $model->data_encerramento = null;

            if ($model->save(false)) {

                // Atualiza pulseira para Em atendimento
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;
                    $pulseira->status = "Em atendimento";
                    $pulseira->save(false);
                }

                // Notificação ao paciente
                $userId = $model->triagem->userprofile_id;
                Notificacao::enviar(
                    $userId,
                    "Consulta iniciada",
                    "A sua consulta foi iniciada.",
                    "Consulta"
                );

                //MQTT — CONSULTA CRIADA
                Yii::$app->mqtt->publish(
                    "consulta/criada/{$model->id}",
                    json_encode([
                        "evento" => "consulta_criada_backend",
                        "consulta_id" => $model->id,
                        "triagem_id" => $model->triagem_id,
                        "userprofile_id" => $model->userprofile_id,
                        "estado" => $model->estado,
                        "hora" => date('Y-m-d H:i:s')
                    ])
                );

                Yii::$app->session->setFlash('success', 'Consulta criada com sucesso!');
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'triagensDisponiveis' => $triagensDisponiveis,
        ]);
    }


     //AJAX TRIAGEM INFO
    public function actionTriagemInfo($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $triagem = Triagem::find()
            ->where(['triagem.id' => $id])
            ->joinWith(['userprofile', 'pulseira'])
            ->one();

        if (!$triagem) {
            return ['error' => 'Triagem não encontrada'];
        }

        return [
            'userprofile_id' => $triagem->userprofile_id,
            'user_nome'      => $triagem->userprofile->nome ?? '—',
        ];
    }

     //EDITAR CONSULTA + MQTT
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $triagensDisponiveis = ArrayHelper::map(
            Triagem::find()->joinWith('pulseira')->where(['not',['pulseira.id'=>null]])->all(),
            'id',
            fn($t) => "Pulseira: " . ($t->pulseira->codigo ?? '—')
        );

        if ($model->load(Yii::$app->request->post())) {

            // Consulta deve ter prescrição
            if (!$model->prescricao) {
                Yii::$app->session->setFlash('error', 'É obrigatório adicionar uma prescrição antes de guardar.');
                return $this->redirect(['update', 'id' => $model->id]);
            }

            // Atualiza datas
            if ($model->estado === Consulta::ESTADO_EM_CURSO) {
                $model->data_encerramento = null;
            }

            if ($model->estado === Consulta::ESTADO_ENCERRADA && empty($model->data_encerramento)) {
                $model->data_encerramento = date('Y-m-d H:i:s');
            }

            if ($model->save(false)) {

                $userId = $model->triagem->userprofile_id;
                $estado = $model->estado;

                // Atualiza pulseira
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;
                    $pulseira->status = $estado === Consulta::ESTADO_ENCERRADA ? "Atendido" : "Em atendimento";
                    $pulseira->save(false);
                }

                // Notificação conforme estado
                if ($estado === Consulta::ESTADO_EM_CURSO) {
                    Notificacao::enviar($userId, "Consulta retomada", "A consulta foi retomada.", "Consulta");
                }

                if ($estado === Consulta::ESTADO_ENCERRADA) {
                    Notificacao::enviar($userId, "Consulta encerrada", "A sua consulta foi encerrada.", "Consulta");
                }

                // MQTT — CONSULTA ATUALIZADA
                Yii::$app->mqtt->publish(
                    "consulta/atualizada/{$model->id}",
                    json_encode([
                        "evento" => "consulta_atualizada_backend",
                        "consulta_id" => $model->id,
                        "estado" => $model->estado,
                        "hora" => date('Y-m-d H:i:s')
                    ])
                );

                // MQTT — CONSULTA ENCERRADA
                if ($estado === Consulta::ESTADO_ENCERRADA) {
                    Yii::$app->mqtt->publish(
                        "consulta/encerrada/{$model->id}",
                        json_encode([
                            "evento" => "consulta_encerrada_backend",
                            "consulta_id" => $model->id,
                            "hora" => date('Y-m-d H:i:s')
                        ])
                    );
                }

                Yii::$app->session->setFlash('success', 'Consulta atualizada com sucesso!');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'triagensDisponiveis' => $triagensDisponiveis,
        ]);
    }

    //HISTÓRICO DE CONSULTAS
    public function actionHistorico()
    {
        $medicoAssignments = Yii::$app->authManager->getUserIdsByRole('medico');

        $medicos = UserProfile::find()
            ->where(['user_id' => $medicoAssignments])
            ->all();

        $dataProvider = new ActiveDataProvider([
            'query' => Consulta::find()
                ->where(['estado' => Consulta::ESTADO_ENCERRADA])
                ->orderBy(['data_encerramento' => SORT_DESC]),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('historico', [
            'medicos' => $medicos,
            'dataProvider' => $dataProvider,
        ]);
    }

     //ENCERRAR CONSULTA + MQTT
    public function actionEncerrar($id)
    {
        $model = $this->findModel($id);

        $model->estado = Consulta::ESTADO_ENCERRADA;
        $model->data_encerramento = date('Y-m-d H:i:s');

        if (Yii::$app->user && Yii::$app->user->identity->userprofile) {
            $model->medicouserprofile_id = Yii::$app->user->identity->userprofile->id;
        }

        $model->save(false);

        if ($model->triagem && $model->triagem->pulseira) {
            $pulseira = $model->triagem->pulseira;
            $pulseira->status = 'Atendido';
            $pulseira->save(false);
        }

        // Notificação
        if ($model->triagem) {
            $userId = $model->triagem->userprofile_id;
            Notificacao::enviar($userId, "Consulta encerrada", "A sua consulta foi encerrada.", "Consulta");
        }

        // MQTT — CONSULTA ENCERRADA
        Yii::$app->mqtt->publish(
            "consulta/encerrada/{$model->id}",
            json_encode([
                "evento" => "consulta_encerrada_backend",
                "consulta_id" => $model->id,
                "hora" => date('Y-m-d H:i:s')
            ])
        );

        Yii::$app->session->setFlash('success', 'Consulta encerrada com sucesso!');
        return $this->redirect(['index']);
    }

     //DELETE CONSULTA + MQTT
    public function actionDelete($id)
    {
        $consulta = $this->findModel($id);

        $triagem = $consulta->triagem;
        $pulseira = $triagem->pulseira ?? null;

        // apagar prescrições
        foreach ($consulta->prescricoes as $prescricao) {
            Prescricaomedicamento::deleteAll([
                'prescricao_id' => $prescricao->id
            ]);
            $prescricao->delete();
        }

        $consulta->delete();

        // apagar triagem
        if ($triagem) {
            $triagem->pulseira_id = null;
            $triagem->save(false);
            $triagem->delete();
        }

        // apagar pulseira
        if ($pulseira) {
            $pulseira->delete();
        }

        // MQTT — CONSULTA APAGADA
        Yii::$app->mqtt->publish(
            "consulta/apagada/{$id}",
            json_encode([
                "evento" => "consulta_apagada_backend",
                "consulta_id" => $id,
                "hora" => date('Y-m-d H:i:s')
            ])
        );

        Yii::$app->session->setFlash('success', 'Consulta, triagem e pulseira eliminadas com sucesso.');
        return $this->redirect(['historico']);
    }


    protected function findModel($id)
    {
        if (($model = Consulta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('A consulta solicitada não existe.');
    }
}
