<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\User;
use common\models\UserProfile;

class PacienteController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Configuração de resposta JSON e Autenticação por URL (?auth_key=...)
        unset($behaviors['authenticator']);
        $behaviors['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // Desativamos as ações padrão para criar as nossas personalizadas
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * VERIFICAÇÃO DE PERMISSÕES (RBAC)
     * Chamado automaticamente pelo Yii ou manualmente nas nossas funções.
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // Administrador pode fazer tudo
        if (Yii::$app->user->can('admin')) {
            return;
        }

        // Regras para Update e View
        if ($action === 'update' || $action === 'view') {
            // Se for o PRÓPRIO paciente a tentar ver/editar o seu perfil -> Permite
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            throw new ForbiddenHttpException("Não tem permissão para aceder a este perfil.");
        }

        // Regras para Create e Delete
        if ($action === 'create' || $action === 'delete') {
            // Apenas admin (ou recepcionista, se tiveres essa role) pode criar/apagar pacientes
            throw new ForbiddenHttpException("Apenas administradores podem gerir registos.");
        }
    }

    /**
     * GET /api/paciente
     * Lista apenas Pacientes.
     */
    public function actionIndex()
    {
        $user = Yii::$app->user;

        //  se for ADMIN -> Vê todos os pacientes
        if ($user->can('admin')) {
            // Procura todos os perfis cujo User associado tenha a role 'paciente'
            $pacientes = UserProfile::find()
                ->alias('p')
                ->innerJoin('user u', 'p.user_id = u.id')
                ->innerJoin('auth_assignment aa', 'aa.user_id = u.id')
                ->where(['aa.item_name' => 'paciente'])
                ->asArray()
                ->all();

            return [
                'total' => count($pacientes),
                'data' => $pacientes,
            ];
        }

        // se for PACIENTE -> Só se vê a si mesmo
        else {
            $meuPerfil = UserProfile::find()
                ->where(['user_id' => $user->id])
                ->asArray()
                ->one();

            if (!$meuPerfil) {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }
            return [$meuPerfil];
        }
    }

    /**
     * GET /api/paciente/view?id=X
     */
    public function actionView($id)
    {
        // Procuramos o perfil pelo ID do UserProfile
        $model = UserProfile::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException("Paciente não encontrado.");
        }

        // Verifica se tem permissão (Admin ou Próprio)
        $this->checkAccess('view', $model);

        return $model;
    }

    /**
     * POST /api/paciente/create
     * Cria User + UserProfile e define role como 'paciente'
     */
    public function actionCreate()
    {
        // Verifica permissão (apenas Admin pode criar aqui)
        $this->checkAccess('create');

        $request = Yii::$app->getRequest();
        $params = $request->getBodyParams();

        // 1. Criar User (Login)
        $user = new User();
        $user->username = $params['username'];
        $user->email = $params['email'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();
        $user->status = 10;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$user->save()) {
                throw new \Exception("Erro ao criar utilizador: " . json_encode($user->errors));
            }

            // Criar Perfil (Dados Paciente)
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->nome = $params['nome'];
            $profile->nif = $params['nif'] ?? null;
            $profile->sns = $params['sns'] ?? null;
            $profile->datanascimento = $params['datanascimento'] ?? null;
            $profile->genero = $params['genero'] ?? null;
            $profile->telefone = $params['telefone'] ?? null;
            $profile->morada = $params['morada'] ?? null;

            if (!$profile->save()) {
                throw new \Exception("Erro ao criar perfil: " . json_encode($profile->errors));
            }

            //  Atribuir Role 'paciente'
            $auth = Yii::$app->authManager;
            $rolePaciente = $auth->getRole('paciente');
            if ($rolePaciente) {
                $auth->assign($rolePaciente, $user->id);
            }

            $transaction->commit();
            Yii::$app->response->statusCode = 201;
            return [
                'status' => true,
                'message' => 'Paciente criado com sucesso',
                'data' => $profile
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 422;
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * POST /api/paciente/update?id=X
     * Atualiza dados do Paciente E do User (email/username)
     */
    public function actionUpdate($id)
    {
        // O ID recebido aqui é o ID do UserProfile
        $profile = UserProfile::findOne($id);

        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        // Verifica permissão (Admin ou Próprio)
        $this->checkAccess('update', $profile);

        $request = Yii::$app->getRequest();
        $params = $request->getBodyParams();
        $user = $profile->user;

        // Atualizar dados do User (se enviados)
        if (isset($params['username'])) $user->username = $params['username'];
        if (isset($params['email']))    $user->email = $params['email'];

        // Atualizar dados do Profile
        if (isset($params['nome']))           $profile->nome = $params['nome'];
        if (isset($params['telefone']))       $profile->telefone = $params['telefone'];
        if (isset($params['nif']))            $profile->nif = $params['nif'];
        if (isset($params['sns']))            $profile->sns = $params['sns'];
        if (isset($params['morada']))         $profile->morada = $params['morada'];
        if (isset($params['datanascimento'])) $profile->datanascimento = $params['datanascimento'];
        if (isset($params['genero']))         $profile->genero = $params['genero'];

        if ($user->validate() && $profile->validate()) {
            $user->save(false);
            $profile->save(false);
            return [
                'status' => true,
                'message' => 'Dados atualizados.',
                'data' => $profile
            ];
        } else {
            return [
                'status' => false,
                'errors' => array_merge($user->errors, $profile->errors)
            ];
        }
    }
}