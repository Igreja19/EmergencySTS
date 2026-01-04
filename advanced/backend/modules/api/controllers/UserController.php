<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use backend\modules\api\controllers\BaseActiveController;

use common\models\User;
use common\models\UserProfile;

class UserController extends BaseActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;
    public $layout = false;

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // Update: apenas admin ou o próprio dono
        if ($action === 'update') {
            if (Yii::$app->user->can('admin')) {
                return;
            }
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            throw new ForbiddenHttpException("Não tem permissão para editar este perfil.");
        }

        // Delete: Apenas admin
        if ($action === 'delete') {
            if (!Yii::$app->user->can('admin')) {
                throw new ForbiddenHttpException("Apenas administradores podem apagar utilizadores.");
            }
        }
    }

    // Criar utilizador via API Admin
    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores podem criar utilizadores.");
        }

        $params = Yii::$app->request->getBodyParams();

        $user = new User();
        $user->username = $params['username'];
        $user->email    = $params['email'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();
        $user->status   = 10;

        if (!$user->save()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $user->getErrors()];
        }

        $profile = new UserProfile();
        $profile->user_id       = $user->id;
        $profile->nome          = $params['nome'];
        $profile->email         = $user->email;
        $profile->nif           = $params['nif'];
        $profile->sns           = $params['sns'];
        $profile->datanascimento= $params['datanascimento'];
        $profile->genero        = $params['genero'];
        $profile->telefone      = $params['telefone'];

        if (!$profile->save()) {
            $user->delete();
            Yii::$app->response->statusCode = 422;
            return ['errors' => $profile->getErrors()];
        }

        $auth = Yii::$app->authManager;
        $roleName = $params['role'] ?? 'paciente';
        $role = $auth->getRole($roleName);
        if ($role) {
            $auth->assign($role, $user->id);
        }

        // MQTT Seguro
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish(
                    "user/criado/{$user->id}",
                    json_encode([
                        'evento'   => 'user_criado',
                        'user_id'  => $user->id,
                        'username' => $user->username,
                        'email'    => $user->email,
                        'nome'     => $profile->nome,
                        'role'     => $roleName,
                        'hora'     => date('Y-m-d H:i:s'),
                    ])
                );
            } catch (\Exception $e) {
                Yii::error("Erro MQTT User Create: " . $e->getMessage());
            }
        }

        Yii::$app->response->statusCode = 201;
        return $profile;
    }

    // GET /api/user
    public function actionIndex()
    {
        // Se for Admin, vê tudo.
        if (Yii::$app->user->can('admin')) {
            $profiles = UserProfile::find()->asArray()->all();
            return [
                'Total de perfis' => count($profiles),
                'Data'            => $profiles,
            ];
        }

        // Se for Médico, Enfermeiro OU PACIENTE:
        // Vê apenas o seu próprio perfil nesta rota.
        $loggedId = Yii::$app->user->id;
        $profile = UserProfile::find()->where(['user_id' => $loggedId])->asArray()->one();

        if (!$profile) {
            throw new NotFoundHttpException("Não foi encontrado um perfil para o utilizador logado.");
        }

        // Retorna diretamente o objeto perfil
        return $profile;
    }

    // GET /api/user/{id}
    public function actionView($id)
    {
        $loggedId = Yii::$app->user->id;
        $profile = UserProfile::find()->where(['id' => $id])->asArray()->one();

        if (!$profile) {
            throw new NotFoundHttpException("Perfil com ID {$id} não encontrado.");
        }

        // Apenas Admin pode ver qualquer perfil via ID.
        // O utilizador comum (Paciente/Médico) só pode ver o seu próprio se coincidir.
        if (!Yii::$app->user->can('admin') && $profile['user_id'] != $loggedId) {
            throw new ForbiddenHttpException("Não tem permissão para ver este perfil.");
        }

        return $profile;
    }

    // DELETE /api/user/{id}
    public function actionDelete($id)
    {
        //  Verificação de permissões
        // Isto vai lançar ForbiddenHttpException se for Médico ou Enfermeiro
        $this->checkAccess('delete');
        
        // Tenta encontrar pelo ID do Perfil primeiro
        $profile = UserProfile::findOne($id);
        
        // Fallback: Se não encontrar perfil, tenta encontrar pelo ID de User
        $user = null;
        if ($profile) {
            $user = User::findOne($profile->user_id);
        } else {
            $user = User::findOne($id);
        }

        if (!$user) {
            throw new NotFoundHttpException("Utilizador não encontrado.");
        }

        // Apagar o User (o Profile apaga-se sozinho por cascata na BD)
        $user->delete();

        // MQTT (Opcional)
        $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
        if ($mqttEnabled && isset(Yii::$app->mqtt)) {
            try {
                Yii::$app->mqtt->publish("user/apagado/{$user->id}", json_encode([
                    'evento' => 'user_apagado',
                    'user_id' => $user->id,
                    'hora' => date('Y-m-d H:i:s')
                ]));
            } catch (\Exception $e) {
                Yii::error("Erro MQTT User Delete: " . $e->getMessage());
            }
        }

        return ['status' => 'success', 'message' => 'Utilizador eliminado.'];
    }
}