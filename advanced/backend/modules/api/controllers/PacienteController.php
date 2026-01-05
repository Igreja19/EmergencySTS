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
    $user = Yii::$app->user;

    // Admin pode tudo
    if ($user->can('admin')) return;

    // Staff (Médico/Enfermeiro) pode ver e listar, mas não apagar
    if ($user->can('medico') || $user->can('enfermeiro')) {
        if (in_array($action, ['index', 'view', 'perfil'])) return;
        if ($action === 'create' || $action === 'delete') throw new ForbiddenHttpException();
        // Update: Enfermeiros/Médicos podem editar dados do paciente? Se não, bloquear aqui.
    }

    // PACIENTE (Onde está o risco)
    if ($user->can('paciente')) {
        // Apenas permite ver/editar o PRÓPRIO perfil
        if (in_array($action, ['view', 'update', 'perfil'])) {
            // Se for 'perfil', não há $model, passa.
            if ($action === 'perfil') return;
            
            // Se for view/update com ID, verificar se o ID pertence ao user logado
            if ($model && $model->user_id !== $user->id) {
                throw new ForbiddenHttpException("Não pode aceder ao perfil de outro paciente.");
            }
            return;
        }
        
        // Paciente não pode fazer 'index' (ver lista de todos) nem 'create' nem 'delete'
        throw new ForbiddenHttpException("Ação não permitida para pacientes.");
    }
}

    // GET /api/paciente (index)
    public function actionIndex()
    {
        // --- SEGURANÇA: Bloquear acesso a pacientes ---
        if (Yii::$app->user->can('paciente')) {
            throw new ForbiddenHttpException("Acesso negado.");
        }

        
        // Filtragem por NIF (código existente)
        $nif = Yii::$app->request->get('nif');
        if (!empty($nif)) {
            $paciente = UserProfile::find()->where(['nif' => $nif])->asArray()->one();
            return $paciente ? [$paciente] : [];
        }

        $pacientes = UserProfile::find()
            ->alias('p')
            ->innerJoin('user u', 'p.user_id = u.id')
            ->innerJoin('auth_assignment aa', 'aa.user_id = u.id')
            ->where(['aa.item_name' => 'paciente'])
            ->asArray()
            ->all();

        return ['total' => count($pacientes), 'data' => $pacientes];
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
        //  Procurar perfil (pelo user_id ou id)
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
            $dados = array_merge($dados, $dados['Paciente']);
        }

        // Atualizar campos
        if (isset($dados['nome']))      $profile->nome      = $dados['nome'];
        if (isset($dados['telefone']))  $profile->telefone  = $dados['telefone'];
        if (isset($dados['nif']))       $profile->nif       = $dados['nif'];
        if (isset($dados['sns']))       $profile->sns       = $dados['sns'];
        if (isset($dados['morada']))    $profile->morada    = $dados['morada'];
        if (isset($dados['datanascimento'])) $profile->datanascimento = $dados['datanascimento'];

        // Atualizar Email
        if (isset($dados['email'])) {
            $user = User::findOne($profile->user_id);
            if ($user) {
                $user->email = $dados['email'];
                $user->save(false);
            }
        }

        // Guardar
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