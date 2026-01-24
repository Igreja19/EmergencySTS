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
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class ConsultaController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                // CONTROLO DE ACESSO
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['index','view','create','update','delete','chart-data', 'historico', 'encerrar'],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['error', 'login'],
                        ],
                        [
                            'actions' => ['index','view','create','update','chart-data', 'historico', 'encerrar'],
                            'allow' => true,
                            'roles' => ['admin', 'medico'],
                        ],
                        [
                            'actions' => ['delete'],
                            'allow' => true,
                            'roles' => ['admin'],
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

    // CRIAR CONSULTA + MQTT SEGURO
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

            $userId = Yii::$app->user->id;
            $auth = Yii::$app->authManager;

            if (
                ($auth->checkAccess($userId, 'medico') || $auth->checkAccess($userId, 'admin')) &&
                Yii::$app->user->identity->userprofile
            ) {
                $model->medicouserprofile_id = Yii::$app->user->identity->userprofile->id;
            }

            if ($model->save(false)) {

                // Atualiza pulseira para Em atendimento
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;
                    $pulseira->status = "Em atendimento";
                    $pulseira->save(false);
                }

                try {
                    if (Yii::$app->has('mqtt')) {
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
                    }
                } catch (\Exception $e) {
                    Yii::warning("Falha MQTT (Create Consulta): " . $e->getMessage());
                }

                Yii::$app->session->setFlash('success', 'Consulta criada com sucesso!');
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'triagensDisponiveis' => $triagensDisponiveis,
        ]);
    }


    // AJAX TRIAGEM INFO
    public function actionTriagemInfo($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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

    // EDITAR CONSULTA + MQTT SEGURO
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $triagensDisponiveis = ArrayHelper::map(
            Triagem::find()->joinWith('pulseira')->where(['not',['pulseira.id'=>null]])->all(),
            'id',
            fn($t) => "Pulseira: " . ($t->pulseira->codigo ?? '—')
        );

        if ($model->load(Yii::$app->request->post())) {

            if (empty($model->medicouserprofile_id)) {

                $userId = Yii::$app->user->id;
                $auth = Yii::$app->authManager;

                if (
                    ($auth->checkAccess($userId, 'medico') || $auth->checkAccess($userId, 'admin')) &&
                    Yii::$app->user->identity->userprofile
                ) {
                    $model->medicouserprofile_id = Yii::$app->user->identity->userprofile->id;
                }
            }

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

                try {
                    if (Yii::$app->has('mqtt')) {
                        Yii::$app->mqtt->publish(
                            "consulta/atualizada/{$model->id}",
                            json_encode([
                                "evento" => "consulta_atualizada_backend",
                                "consulta_id" => $model->id,
                                "estado" => $model->estado,
                                "hora" => date('Y-m-d H:i:s')
                            ])
                        );

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
                    }
                } catch (\Exception $e) {
                    Yii::warning("Falha MQTT (Update Consulta): " . $e->getMessage());
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

    // HISTÓRICO DE CONSULTAS
    public function actionHistorico()
    {
        $user = Yii::$app->user;

        $query = Consulta::find()
            ->where(['estado' => Consulta::ESTADO_ENCERRADA])
            ->with(['userprofile', 'medico'])
            ->orderBy(['data_encerramento' => SORT_DESC]);

        $medicos = [];

        if ($user->can('admin')) {
            $medicoUserIds = Yii::$app->authManager->getUserIdsByRole('medico');

            $medicos = UserProfile::find()
                ->where(['user_id' => $medicoUserIds])
                ->all();

            $filtroMedicoId = Yii::$app->request->get('medico');

            $query->andFilterWhere(['medicouserprofile_id' => $filtroMedicoId]);
        }
        elseif ($user->can('medico')) {
            $userProfile = $user->identity->userprofile;
            if ($userProfile) {
                $query->andWhere(['medicouserprofile_id' => $userProfile->id]);
            } else {
                $query->andWhere(['0=1']);
            }
        }
        else {
            $query->andWhere(['0=1']);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('historico', [
            'medicos' => $medicos,
            'dataProvider' => $dataProvider,
        ]);
    }

    // ENCERRAR CONSULTA + MQTT SEGURO
    public function actionEncerrar($id)
    {
        $model = $this->findModel($id);

        // Não pode encerrar sem prescrição
        if (empty($model->prescricoes)) {
            Yii::$app->session->setFlash(
                'error',
                'Não é possível encerrar a consulta sem pelo menos uma prescrição.'
            );
            return $this->redirect(['index']);
        }

        $model->estado = Consulta::ESTADO_ENCERRADA;
        $model->data_encerramento = date('Y-m-d H:i:s');

        // Médico
        if (Yii::$app->user && Yii::$app->user->identity->userprofile) {

            $medicoUser = Yii::$app->user->identity;
            $medicoProfile = $medicoUser->userprofile;

            $model->medicouserprofile_id = $medicoProfile->id;

            // Nome do perfil OU username do user
            $model->medico_nome = $medicoProfile->nome
                ?: $medicoUser->username
                    ?: 'Profissional de Saúde';
        }

        $model->save(false);

        if ($model->triagem && $model->triagem->pulseira) {
            $pulseira = $model->triagem->pulseira;
            $pulseira->status = 'Atendido';
            $pulseira->save(false);
        }

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "consulta/encerrada/{$model->id}",
                    json_encode([
                        "evento" => "consulta_encerrada_backend",
                        "consulta_id" => $model->id,
                        "hora" => date('Y-m-d H:i:s')
                    ])
                );
            }
        } catch (\Exception $e) {
            Yii::warning("Falha MQTT (Encerrar Consulta): " . $e->getMessage());
        }

        Yii::$app->session->setFlash('success', 'Consulta encerrada com sucesso!');
        return $this->redirect(['index']);
    }

    // DELETE CONSULTA + MQTT SEGURO
    public function actionDelete($id)
    {
        $consulta = $this->findModel($id);

        // Guardar dados antes de apagar (caso precises da info)
        $consultaNome = $consulta->nome ?? "Consulta #$id";

        $triagem = $consulta->triagem;
        $pulseira = $triagem->pulseira ?? null;

        // apagar prescrições
        foreach ($consulta->prescricoes as $prescricao) {
            Prescricaomedicamento::deleteAll([
                'prescricao_id' => $prescricao->id
            ]);
            $prescricao->delete();
        }

        // apagar consulta
        $consulta->delete();

        // Libertar TODAS as triagens que usam esta pulseira (segurança extra)
        if ($pulseira) {
            \common\models\Triagem::updateAll(
                ['pulseira_id' => null],
                ['pulseira_id' => $pulseira->id]
            );
        }

        // Agora apaga triagem
        if ($triagem) {
            $triagem->delete();
        }

        // apagar pulseira
        if ($pulseira) {
            $pulseira->delete();
        }

        try {
            if (Yii::$app->has('mqtt')) {
                Yii::$app->mqtt->publish(
                    "consulta/apagada/{$id}",
                    json_encode([
                        "evento" => "consulta_apagada_backend",
                        "consulta_id" => $id,
                        "hora" => date('Y-m-d H:i:s')
                    ])
                );
            }
        } catch (\Exception $e) {
            Yii::warning("Falha MQTT (Delete Consulta): " . $e->getMessage());
        }

        // Notificação envia para o ADMIN (não para o user criado)
        $adminProfileId = Yii::$app->user->identity->userprofile->id;

        Notificacao::enviar(
            $adminProfileId,
            "Consulta eliminada",
            "A '{$consultaNome}' foi apagada do histórico.",
            "Geral"
        );

        Yii::$app->session->setFlash('success', 'Consulta, triagem e pulseira eliminadas com sucesso.');
        return $this->redirect(['historico']);
    }

    public function actionPdf($id)
    {
        $consulta = $this->findModel($id);

        // Só permitir PDF se consulta estiver encerrada
        if ($consulta->estado !== Consulta::ESTADO_ENCERRADA) {
            Yii::$app->session->setFlash(
                'error',
                'Só é possível gerar o PDF após a consulta estar encerrada.'
            );
            return $this->redirect(['view', 'id' => $consulta->id]);
        }

        $prescricao = $consulta->prescricao;
        if (!$prescricao) {
            Yii::$app->session->setFlash(
                'error',
                'Esta consulta não tem prescrição associada.'
            );
            return $this->redirect(['view', 'id' => $consulta->id]);
        }

        $medicoNome = $consulta->medico_nome ?? 'Profissional de Saúde';

        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 12,
            'default_font'      => 'dejavusans',
        ]);

        $css = file_get_contents(
            Yii::getAlias('@frontend/web/css/consulta/pdf.css')
        );
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

        $html = $this->renderPartial('pdf', [
            'consulta'   => $consulta,
            'prescricao' => $prescricao,
            'medicoNome' => $medicoNome,
        ]);

        if (ob_get_length()) {
            ob_end_clean();
        }

        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        return $mpdf->Output(
            "Consulta_{$consulta->id}.pdf",
            \Mpdf\Output\Destination::DOWNLOAD
        );

    }


    protected function findModel($id)
    {
        if (($model = Consulta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('A consulta solicitada não existe.');
    }
}