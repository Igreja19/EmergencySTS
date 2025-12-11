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

        unset($behaviors['authenticator']);
        $behaviors['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;

        // autenticação via ?auth_key=
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // admin pode tudo
        if (Yii::$app->user->can('admin')) {
            return;
        }

        if ($action === 'view' || $action === 'update') {
            if ($model && $model->user_id == Yii::$app->user->id) {
                return; // paciente a ver/editar os seus próprios dados
            }
            throw new ForbiddenHttpException("Não tem permissão para aceder a este perfil.");
        }

        if ($action === 'create' || $action === 'delete') {
            throw new ForbiddenHttpException("Apenas administradores podem gerir registos.");
        }
    }

    /**
     * GET /api/paciente
     * Agora aceita filtro por NIF → ?nif=123456789
     */
    public function actionIndex()
    {
        $user = Yii::$app->user;
        $request = Yii::$app->request;
        $nif = $request->get('nif');  // <-- LER O NIF DA URL

        // 🔍 Se tiver nif → procurar um paciente específico
        if (!empty($nif)) {
            $paciente = UserProfile::find()
                ->where(['nif' => $nif])
                ->asArray()
                ->one();

            if (!$paciente) {
                return []; // devolve array vazio
            }

            return [$paciente];
        }

        // 🔹 ADMIN → vê todos os pacientes
        if ($user->can('admin')) {
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

        // 🔹 PACIENTE → só se vê a si próprio
        $meuPerfil = UserProfile::find()
            ->where(['user_id' => $user->id])
            ->asArray()
            ->one();

        return [$meuPerfil];
    }

    /**
     * GET /api/paciente/view?id=X
     */
    public function actionView($id)
    {
        $model = UserProfile::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException("Paciente não encontrado.");
        }

        $this->checkAccess('view', $model);

        return $model;
    }

    /**
     * POST /api/paciente/create
     */
    public function actionCreate()
    {
        $this->checkAccess('create');

        $params = Yii::$app->request->getBodyParams();

        $user = new User();
        $user->username = $params['username'];
        $user->email = $params['email'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();
        $user->status = 10;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$user->save()) {
                throw new \Exception(json_encode($user->errors));
            }

            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->nome = $params['nome'] ?? null;
            $profile->nif = $params['nif'] ?? null;
            $profile->sns = $params['sns'] ?? null;
            $profile->telefone = $params['telefone'] ?? null;

            if (!$profile->save()) {
                throw new \Exception(json_encode($profile->errors));
            }

            $auth = Yii::$app->authManager;
            $rolePaciente = $auth->getRole('paciente');
            $auth->assign($rolePaciente, $user->id);

            $transaction->commit();
            Yii::$app->response->statusCode = 201;

            return [
                'status'  => true,
                'message' => 'Paciente criado com sucesso',
                'data'    => $profile,
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 422;

            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * POST /api/paciente/update?id=X
     */
    public function actionUpdate($id)
    {
        $profile = UserProfile::findOne($id);

        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        $this->checkAccess('update', $profile);

        $params = Yii::$app->request->getBodyParams();
        $user = $profile->user;

        // user
        if (isset($params['username'])) $user->username = $params['username'];
        if (isset($params['email']))    $user->email = $params['email'];

        // perfil
        if (isset($params['nome']))      $profile->nome = $params['nome'];
        if (isset($params['telefone']))  $profile->telefone = $params['telefone'];
        if (isset($params['nif']))       $profile->nif = $params['nif'];
        if (isset($params['sns']))       $profile->sns = $params['sns'];

        if ($user->validate() && $profile->validate()) {
            $user->save(false);
            $profile->save(false);

            return [
                'status'  => true,
                'message' => 'Dados atualizados.',
                'data'    => $profile,
            ];
        }

        return [
            'status' => false,
            'errors' => array_merge($user->errors, $profile->errors),
        ];
    }
    /**
     * GET /api/paciente/perfil?id=X
     * X = user_id
     */
    public function actionPerfil($id)
    {
        $user = User::findOne($id);

        if (!$user) {
            return [
                "status" => false,
                "message" => "Utilizador não encontrado"
            ];
        }

        $profile = $user->profile;

        if (!$profile) {
            return [
                "status" => false,
                "message" => "Perfil não encontrado"
            ];
        }

        return [
            "status" => true,
            "id" => $profile->id,
            "user_id" => $user->id,
            "nome" => $profile->nome,
            "email" => $user->email,
            "morada" => $profile->morada,
            "nif" => $profile->nif,
            "sns" => $profile->sns,
            "datanascimento" => $profile->datanascimento,
            "genero" => $profile->genero,
            "telefone" => $profile->telefone
        ];
    }
}
