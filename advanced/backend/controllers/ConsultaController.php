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
                ->groupBy('pulseira.id') // evita duplicados
                ->all(),
            'id',
            function($t) {
                $cor = $t->pulseira->prioridade ?? '—';
                $codigo = $t->pulseira->codigo ?? 'Sem código';
                return "Pulseira: {$cor} ({$codigo})";
            }
        );

        // ⬇️ SUPER IMPORTANTE — carregar o POST!
        if ($model->load(Yii::$app->request->post())) {

            // valores automáticos
            $model->data_consulta = date('Y-m-d H:i:s');
            $model->estado = Consulta::ESTADO_EM_CURSO;
            $model->data_encerramento = null;

            if ($model->save(false)) {

                // Atualizar pulseira
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;
                    $pulseira->status = "Em atendimento";
                    $pulseira->save(false);
                }

                // Notificação para o paciente
                $userId = $model->triagem->userprofile_id;
                Notificacao::enviar(
                    $userId,
                    "Consulta iniciada",
                    "A sua consulta foi iniciada.",
                    "Consulta"
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

    /**
     * AJAX — devolve info da triagem
     */
    public function actionTriagemInfo($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $triagem = Triagem::find()
            ->where(['triagem.id' => $id])
            ->joinWith(['userprofile', 'pulseira']) // garante carregamento
            ->one();

        if (!$triagem) {
            return ['error' => 'Triagem não encontrada'];
        }

        return [
            'userprofile_id' => $triagem->userprofile_id ?? $triagem->pulseira->userprofile_id ?? null,
            'user_nome'      => $triagem->userprofile->nome
                ?? $triagem->pulseira->userprofile->nome ?? '—',
        ];
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

                $userId = $model->triagem->userprofile_id;  // paciente
                $estado = $model->estado;

                // Atualizar estado da pulseira
                if ($model->triagem && $model->triagem->pulseira) {
                    $pulseira = $model->triagem->pulseira;

                    $pulseira->status =
                        $estado === Consulta::ESTADO_ENCERRADA
                            ? "Atendido"
                            : "Em atendimento";

                    $pulseira->save(false);
                }

                // 🔥 Notificações baseadas no estado
                if ($estado === Consulta::ESTADO_EM_CURSO) {
                    Notificacao::enviar(
                        $userId,
                        "Consulta retomada",
                        "A consulta foi retomada.",
                        "Consulta"
                    );
                }

                if ($estado === Consulta::ESTADO_ENCERRADA) {
                    Notificacao::enviar(
                        $userId,
                        "Consulta encerrada",
                        "A consulta foi encerrada.",
                        "Consulta"
                    );
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
        //  IDs dos médicos via RBAC
        $medicoAssignments = Yii::$app->authManager->getUserIdsByRole('medico');

        // Perfis dos médicos
        $medicos = UserProfile::find()
            ->where(['user_id' => $medicoAssignments])
            ->all();

        // Criar o dataProvider para o GridView
        $dataProvider = new ActiveDataProvider([
            'query' => \common\models\Consulta::find()
                ->where(['estado' => 'Encerrada'])
                ->orderBy(['data_encerramento' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('historico', [
            'medicos' => $medicos,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEncerrar($id)
    {
        $model = $this->findModel($id);

        $model->estado = Consulta::ESTADO_ENCERRADA;
        $model->data_encerramento = date('Y-m-d H:i:s');
        $model->save(false);

        if ($model->triagem && $model->triagem->pulseira) {
            $pulseira = $model->triagem->pulseira;
            $pulseira->status = 'Atendido';
            $pulseira->save(false);
        }

        // 🔔 Notificação ao paciente
        if ($model->triagem) {
            $userId = $model->triagem->userprofile_id;

            Notificacao::enviar(
                $userId,
                "Consulta encerrada",
                "A sua consulta foi encerrada.",
                "Consulta"
            );
        }

        Yii::$app->session->setFlash('success', 'Consulta encerrada com sucesso!');
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $consulta = $this->findModel($id);

        $triagem = $consulta->triagem;
        $pulseira = $triagem->pulseira ?? null;

        foreach ($consulta->prescricoes as $prescricao) {

            Prescricaomedicamento::deleteAll([
                'prescricao_id' => $prescricao->id
            ]);

            $prescricao->delete();
        }

        $consulta->delete();

        if ($triagem) {
            $triagem->delete();
        }

        if ($pulseira) {
            $pulseira->delete();
        }

        Yii::$app->session->setFlash('success', 'Consulta, triagem, prescrição e pulseira eliminadas com sucesso.');
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
