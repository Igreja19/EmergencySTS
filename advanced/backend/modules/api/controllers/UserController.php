<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\data\ActiveDataProvider;
use backend\modules\api\controllers\BaseActiveController;

use common\models\User;
use common\models\UserProfile;

class UserController extends BaseActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    public function actions()
    {
        $actions = parent::actions();
        // Removemos para personalizar tudo
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * GET /api/user
     * Admin: Vê lista paginada de todos.
     * Outros: Vê apenas o seu próprio perfil.
     */
    public function actionIndex()
    {
        // CASO 1: Admin vê tudo (com paginação, igual aos outros controllers)
        if (Yii::$app->user->can('admin')) {
            return new ActiveDataProvider([
                'query' => UserProfile::find(),
                'pagination' => false, //=> ['pageSize' => 20],
            ]);
        }

        // CASO 2: Utilizador normal só vê o seu
        $loggedId = Yii::$app->user->id;
        $profile = UserProfile::find()->where(['user_id' => $loggedId])->one();

        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        return $profile;
    }

    /**
     * GET /api/user/{id}
     */
    public function actionView($id)
    {
        // Verificação de permissão
        if (!Yii::$app->user->can('admin')) {
            $loggedProfile = UserProfile::findOne(['user_id' => Yii::$app->user->id]);
            // Se não for admin, o ID solicitado TEM de ser o ID do perfil do utilizador logado
            if (!$loggedProfile || $loggedProfile->id != $id) {
                throw new ForbiddenHttpException("Acesso negado a este perfil.");
            }
        }

        $profile = UserProfile::findOne($id);
        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        return $profile;
    }

    /**
     * POST /api/user
     * Apenas Admin cria utilizadores nesta rota
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem criar utilizadores.");
        }

        $params = Yii::$app->request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction(); // Transação segura

        try {
            // 1. Criar User (Login)
            $user = new User();
            $user->username = $params['username'];
            $user->email    = $params['email'];
            $user->setPassword($params['password']);
            $user->generateAuthKey();
            $user->status   = 10; // Active

            if (!$user->save()) {
                throw new \Exception("Erro user: " . json_encode($user->errors));
            }

            // 2. Criar Perfil
            $profile = new UserProfile();
            $profile->user_id       = $user->id;
            $profile->nome          = $params['nome'];
            $profile->email         = $user->email;
            $profile->nif           = $params['nif'] ?? null;
            $profile->sns           = $params['sns'] ?? null;
            $profile->datanascimento= $params['datanascimento'] ?? null;
            $profile->genero        = $params['genero'] ?? null;
            $profile->telefone      = $params['telefone'] ?? null;

            if (!$profile->save()) {
                throw new \Exception("Erro perfil: " . json_encode($profile->errors));
            }

            // 3. Atribuir Role
            $auth = Yii::$app->authManager;
            $roleName = $params['role'] ?? 'paciente';
            $role = $auth->getRole($roleName);
            if ($role) {
                $auth->assign($role, $user->id);
            }

            $transaction->commit();

            // MQTT
            $this->safeMqttPublish("user/criado/{$user->id}", [
                'evento'   => 'user_criado',
                'user_id'  => $user->id,
                'role'     => $roleName,
                'hora'     => date('Y-m-d H:i:s'),
            ]);

            Yii::$app->response->statusCode = 201;
            return $profile;

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * PUT /api/user/{id}
     * Admin: Edita qualquer um.
     * User: Edita o próprio (mas não muda username/role).
     */
    public function actionUpdate($id)
    {
        $profile = UserProfile::findOne($id);
        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        // Segurança: Admin ou o Próprio
        if (!Yii::$app->user->can('admin')) {
            if ($profile->user_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não pode editar este perfil.");
            }
        }

        $data = Yii::$app->request->post();

        // Carrega dados para o perfil (Nome, Morada, Telefone, etc)
        $profile->load($data, '');

        if ($profile->save()) {

            // MQTT
            $this->safeMqttPublish("user/atualizado/{$profile->user_id}", [
                'evento'  => 'user_atualizado',
                'user_id' => $profile->user_id,
                'hora'    => date('Y-m-d H:i:s'),
            ]);

            return $profile;
        }

        return ['errors' => $profile->errors];
    }

    /**
     * DELETE /api/user/{id}
     * Apenas Admin
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem apagar contas.");
        }

        // Tenta achar pelo Profile ID (rota padrão REST)
        $profile = UserProfile::findOne($id);
        $user = null;

        if ($profile) {
            $user = User::findOne($profile->user_id);
        } else {
            // Se não achar perfil, tenta achar user direto (caso perfil já tenha sido apagado)
            $user = User::findOne($id);
        }

        if (!$user) {
            throw new NotFoundHttpException("Utilizador não encontrado.");
        }

        $userId = $user->id; // Guarda ID para MQTT
        $user->delete(); // Cascade deve apagar o profile

        // MQTT
        $this->safeMqttPublish("user/apagado/{$userId}", [
            'evento'  => 'user_apagado',
            'user_id' => $userId,
            'hora'    => date('Y-m-d H:i:s')
        ]);

        return ['status' => 'success', 'message' => 'Utilizador eliminado.'];
    }

    // Helper MQTT
    protected function safeMqttPublish($topic, $payload)
    {
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish($topic, json_encode($payload));
            } catch (\Exception $e) {
                Yii::error("Erro MQTT ({$topic}): " . $e->getMessage());
            }
        }
    }
}