<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\auth\QueryParamAuth;
use common\models\Triagem;
use common\models\UserProfile;
use common\models\Pulseira;

class TriagemController extends ActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Remove autenticação padrão para usar a nossa configuração
        unset($behaviors['authenticator']);

        // Força sempre resposta em JSON
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Configura autenticação por ?auth_key=...
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        // Desativamos as ações automáticas para usarmos a nossa lógica personalizada
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    //  LISTAR TRIAGENS (GET /api/triagem)
    public function actionIndex()
    {
        $user = Yii::$app->user;

        // 1. Se for Profissional de Saúde (Admin/Médico/Enfermeiro) vê TODAS
        if ($user->can('admin') || $user->can('medico') || $user->can('enfermeiro')) {
            $triagens = Triagem::find()->asArray()->all();
        }
        // 2. Se for Paciente, vê SÓ AS SUAS
        else {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) {
                throw new NotFoundHttpException("Perfil de utilizador não encontrado.");
            }

            $triagens = Triagem::find()
                ->where(['userprofile_id' => $profile->id])
                ->orderBy(['datatriagem' => SORT_DESC]) // Mais recentes primeiro
                ->asArray()
                ->all();
        }

        return [
            'status' => 'success',
            'total' => count($triagens),
            'data' => $triagens,
        ];
    }

    //  VER UMA TRIAGEM (GET /api/triagem/{id})
    public function actionView($id)
    {
        $triagem = Triagem::find()->where(['id' => $id])->asArray()->one();

        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        // Segurança: Paciente só pode ver se for dele
        $user = Yii::$app->user;
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if ($triagem['userprofile_id'] != $profile->id) {
                throw new ForbiddenHttpException("Não tem permissão para ver esta triagem.");
            }
        }

        return [
            'status' => 'success',
            'data' => $triagem,
        ];
    }

    //  CRIAR TRIAGEM + PULSEIRA AUTOMÁTICA (POST /api/triagem)
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $user = Yii::$app->user;

        // 1. Identificar o Paciente
        $profile = UserProfile::findOne(['user_id' => $user->id]);
        if (!$profile) {
            throw new BadRequestHttpException("Utilizador sem perfil associado. Complete o perfil primeiro.");
        }

        // 2. Iniciar Transação (Para garantir que Triagem e Pulseira são criadas juntas)
        $transaction = Yii::$app->db->beginTransaction();

        try {
            //Cria Triagem
            $triagem = new Triagem();
            $triagem->load($data, '');//Carrega os dados do POST
            $triagem->userprofile_id = $profile->id;
            $triagem->datatriagem = date('Y-m-d H:i:s');
            if (empty($triagem->queixaprincipal) || empty($triagem->descricaosintomas)) {
                throw new BadRequestHttpException('Campos obrigatórios em falta: queixaprincipal, descricaosintomas.');
            }
            // Tenta guardar a triagem (ainda sem pulseira_id)
            if (!$triagem->save()) {
                throw new \Exception('Erro ao guardar triagem: ' . json_encode($triagem->errors));
            }

            // Gera pulseira automática
            $pulseira = new Pulseira();
            $pulseira->userprofile_id = $profile->id;
            // Gera um código de senha (ex: P-17654...)
            $pulseira->codigo = 'P-' . strtoupper(substr(uniqid(), -5));
            $pulseira->prioridade = 'Pendente'; // Começa como Pendente
            $pulseira->status = 'Em espera';    // Começa em espera
            $pulseira->tempoentrada = date('Y-m-d H:i:s');

            if (!$pulseira->save()) {
                throw new \Exception('Erro ao gerar pulseira: ' . json_encode($pulseira->errors));
            }

            // Pulseira liga a triagem
            $triagem->pulseira_id = $pulseira->id;
            if (!$triagem->save()) {
                throw new \Exception('Erro ao associar pulseira à triagem.');
            }

            // Se tudo correu bem, confirma na base de dados
            $transaction->commit();

            return [
                'status' => 'success',
                'message' => 'Triagem submetida com sucesso. Dirija-se à sala de espera.',
                'data' => [
                    'triagem' => $triagem,
                    'pulseira' => $pulseira // O frontend recebe logo a senha criada
                ]
            ];

        } catch (\Exception $e) {
            // Se algo falhou, cancela tudo (apaga registos parciais)
            $transaction->rollBack();
            Yii::$app->response->statusCode = 422;
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    //  ATUALIZAR (PUT /api/triagem/{id})
    public function actionUpdate($id)
    {
        // Apenas profissionais
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais de saúde podem alterar triagens.");
        }

        $triagem = Triagem::findOne($id);
        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        $data = Yii::$app->request->post();
        $triagem->load($data, '');

        if ($triagem->save()) {
            return [
                'status' => 'success',
                'message' => 'Triagem atualizada.',
                'data' => $triagem
            ];
        }

        return ['status' => 'error', 'errors' => $triagem->getErrors()];
    }


    //  APAGAR (DELETE /api/triagem/{id})
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin') && !Yii::$app->user->can('enfermeiro')) {
            throw new ForbiddenHttpException("Apenas administradores e enfermeiros podem apagar registos de triagem.");
        }

        $triagem = Triagem::findOne($id);
        if ($triagem) {
            // Tratar da Pulseira Associada
            if ($triagem->pulseira_id) {
                $pulseira = Pulseira::findOne($triagem->pulseira_id);
                $triagem->pulseira_id = null;
                $triagem->save(false); // Guardar sem validar, só para limpar o ID
                // Agora já podemos apagar a pulseira em segurança
                if ($pulseira) {
                    $pulseira->delete();
                }
            }
            $triagem->delete();
            return [
                'status' => 'success',
                'message' => 'Triagem e pulseira associada apagadas com sucesso.'
            ];
        }
        throw new NotFoundHttpException("Triagem não encontrada.");
    }
}