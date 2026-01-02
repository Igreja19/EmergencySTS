<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use backend\modules\api\controllers\BaseActiveController; // <--- Importante

use common\models\User;
use common\models\UserProfile;

class PacienteController extends BaseActiveController // <--- Herança segura
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    // NOTA: behaviors() removido porque herda do BaseActiveController

    /**
     * Autoriza POST, PUT e PATCH na ação update.
     */
    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['update'] = ['POST', 'PUT', 'PATCH'];
        return $verbs;
    }

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // Se for admin, pode tudo
        if (Yii::$app->user->can('admin')) {
            return;
        }

        // O BaseActiveController já bloqueou os pacientes.
        // Aqui garantimos que Médicos/Enfermeiros podem ver, mas não apagar.
        if ($action === 'view' || $action === 'update' || $action === 'perfil' || $action === 'index') {
            return;
        }

        if ($action === 'create' || $action === 'delete') {
            throw new ForbiddenHttpException("Apenas administradores podem criar ou apagar registos.");
        }
    }

    // GET /api/paciente (index)
    public function actionIndex()
    {
        // Verifica se é Admin (para ver lista completa) ou Profissional
        // Filtragem por NIF
        $nif = Yii::$app->request->get('nif');

        if (!empty($nif)) {
            $paciente = UserProfile::find()->where(['nif' => $nif])->asArray()->one();
            return $paciente ? [$paciente] : [];
        }

        // Se for Admin, Médico ou Enfermeiro, mostra a lista de pacientes
        $pacientes = UserProfile::find()
            ->alias('p')
            ->innerJoin('user u', 'p.user_id = u.id')
            ->innerJoin('auth_assignment aa', 'aa.user_id = u.id')
            ->where(['aa.item_name' => 'paciente'])
            ->asArray()
            ->all();

        return ['total' => count($pacientes), 'data' => $pacientes];
    }

    // GET /api/paciente/view?id=X
    public function actionView($id)
    {
        $model = UserProfile::findOne($id);
        if (!$model) throw new NotFoundHttpException("Paciente não encontrado.");
        $this->checkAccess('view', $model);
        return $model;
    }

    // GET /api/paciente/perfil
    public function actionPerfil()
    {
        $userId = Yii::$app->user->id;
        $perfil = UserProfile::find()->where(['user_id' => $userId])->asArray()->one();

        if (!$perfil) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        $user = User::findOne($userId);
        if ($user) {
            $perfil['email'] = $user->email;
        }

        return $perfil;
    }

    // POST /api/paciente/create
    public function actionCreate()
    {
        $this->checkAccess('create');

        $params = Yii::$app->request->getBodyParams();

        $user = new User();
        $user->username = $params['username'];
        $user->email    = $params['email'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();
        $user->status   = 10;

        $tx = Yii::$app->db->beginTransaction();

        try {
            if (!$user->save()) throw new \Exception(json_encode($user->errors));

            $profile = new UserProfile();
            $profile->user_id  = $user->id;
            $profile->nome     = $params['nome'] ?? null;
            $profile->nif      = $params['nif'] ?? null;
            $profile->sns      = $params['sns'] ?? null;
            $profile->telefone = $params['telefone'] ?? null;

            if (!$profile->save()) throw new \Exception(json_encode($profile->errors));

            $auth = Yii::$app->authManager;
            $rolePaciente = $auth->getRole('paciente');
            $auth->assign($rolePaciente, $user->id);

            $tx->commit();
            Yii::$app->response->statusCode = 201;

            // MQTT Seguro
            $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
            if ($mqttEnabled && isset(Yii::$app->mqtt)) {
                try {
                    Yii::$app->mqtt->publish("user/criado/{$user->id}", json_encode([
                        'evento'   => 'user_criado',
                        'user_id'  => $user->id,
                        'username' => $user->username,
                        'email'    => $user->email,
                        'nome'     => $profile->nome,
                        'role'     => 'paciente',
                        'hora'     => date('Y-m-d H:i:s'),
                    ]));
                } catch (\Exception $e) {
                    Yii::error("Erro MQTT Create Paciente: " . $e->getMessage());
                }
            }

            return ['status' => true, 'message' => 'Paciente criado', 'data' => $profile];

        } catch (\Exception $e) {
            $tx->rollBack();
            Yii::$app->response->statusCode = 422;
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }


    // POST /api/paciente/update?id=X
    public function actionUpdate($id)
    {
        // 1. Procurar perfil (pelo user_id ou id)
        $profile = UserProfile::findOne(['user_id' => $id]);
        if (!$profile) {
            $profile = UserProfile::findOne($id);
        }

        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        $this->checkAccess('update', $profile);

        // Ler JSON diretamente
        $dados = Yii::$app->request->getBodyParams();

        // Suporte legacy
        if (isset($dados['Paciente'])) {
            $dados = $dados['Paciente'];
        }

        // 3. Atualizar campos
        if (isset($dados['nome']))      $profile->nome      = $dados['nome'];
        if (isset($dados['telefone']))  $profile->telefone  = $dados['telefone'];
        if (isset($dados['nif']))       $profile->nif       = $dados['nif'];
        if (isset($dados['sns']))       $profile->sns       = $dados['sns'];
        if (isset($dados['morada']))    $profile->morada    = $dados['morada'];
        if (isset($dados['datanascimento'])) $profile->datanascimento = $dados['datanascimento'];

        // 4. Atualizar Email
        if (isset($dados['email'])) {
            $user = User::findOne($profile->user_id);
            if ($user) {
                $user->email = $dados['email'];
                $user->save(false);
            }
        }

        // 5. Guardar
        if ($profile->save()) {

            // MQTT Seguro
            $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
            if ($mqttEnabled && isset(Yii::$app->mqtt)) {
                try {
                    $user = User::findOne($profile->user_id);
                    if ($user) {
                        Yii::$app->mqtt->publish(
                            "user/atualizado/{$profile->user_id}",
                            json_encode([
                                'evento'   => 'user_atualizado',
                                'user_id'  => $profile->user_id,
                                'username' => $user->username,
                                'email'    => $user->email,
                                'nome'     => $profile->nome,
                                'hora'     => date('Y-m-d H:i:s'),
                            ])
                        );
                    }
                } catch (\Exception $e) {
                    Yii::error("Erro no MQTT Update Paciente: " . $e->getMessage());
                }
            }

            return $profile;
        } else {
            Yii::$app->response->statusCode = 422;
            return $profile->getErrors();
        }
    }
}