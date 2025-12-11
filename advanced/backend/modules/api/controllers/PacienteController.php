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
        $b = parent::behaviors();
        unset($b['authenticator']);

        $b['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;
        $b['authenticator'] = [
            'class'      => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $b;
    }

    public function actions()
    {
        $a = parent::actions();
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (Yii::$app->user->can('admin')) {
            return;
        }

        if ($action === 'view' || $action === 'update') {
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            throw new ForbiddenHttpException("Não tem permissão para aceder a este perfil.");
        }

        if ($action === 'create' || $action === 'delete') {
            throw new ForbiddenHttpException("Apenas administradores podem gerir registos.");
        }
    }

    // GET /api/paciente (com filtro nif opcional)
    public function actionIndex()
    {
        $user = Yii::$app->user;
        $nif = Yii::$app->request->get('nif');

        if (!empty($nif)) {
            $paciente = UserProfile::find()->where(['nif' => $nif])->asArray()->one();
            if (!$paciente) {
                return [];
            }
            return [$paciente];
        }

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
                'data'  => $pacientes,
            ];
        }

        $meuPerfil = UserProfile::find()->where(['user_id' => $user->id])->asArray()->one();
        return [$meuPerfil];
    }

    // GET /api/paciente/view?id=X
    public function actionView($id)
    {
        $model = UserProfile::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Paciente não encontrado.");
        }

        $this->checkAccess('view', $model);
        return $model;
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
            if (!$user->save()) {
                throw new \Exception(json_encode($user->errors));
            }

            $profile = new UserProfile();
            $profile->user_id  = $user->id;
            $profile->nome     = $params['nome'] ?? null;
            $profile->nif      = $params['nif'] ?? null;
            $profile->sns      = $params['sns'] ?? null;
            $profile->telefone = $params['telefone'] ?? null;

            if (!$profile->save()) {
                throw new \Exception(json_encode($profile->errors));
            }

            $auth = Yii::$app->authManager;
            $rolePaciente = $auth->getRole('paciente');
            $auth->assign($rolePaciente, $user->id);

            $tx->commit();
            Yii::$app->response->statusCode = 201;

            // MQTT – paciente criado
            Yii::$app->mqtt->publish(
                "user/criado/{$user->id}",
                json_encode([
                    'evento'   => 'user_criado',
                    'user_id'  => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'nome'     => $profile->nome,
                    'role'     => 'paciente',
                    'hora'     => date('Y-m-d H:i:s'),
                ])
            );

            return [
                'status'  => true,
                'message' => 'Paciente criado com sucesso',
                'data'    => $profile,
            ];

        } catch (\Exception $e) {
            $tx->rollBack();
            Yii::$app->response->statusCode = 422;
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    // POST /api/paciente/update?id=X
    public function actionUpdate($id)
    {
        $profile = UserProfile::findOne($id);
        if (!$profile) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        $this->checkAccess('update', $profile);

        $params = Yii::$app->request->getBodyParams();
        $user   = $profile->user;

        if (isset($params['username'])) $user->username = $params['username'];
        if (isset($params['email']))    $user->email    = $params['email'];

        if (isset($params['nome']))      $profile->nome      = $params['nome'];
        if (isset($params['telefone']))  $profile->telefone  = $params['telefone'];
        if (isset($params['nif']))       $profile->nif       = $params['nif'];
        if (isset($params['sns']))       $profile->sns       = $params['sns'];

        if ($user->validate() && $profile->validate()) {
            $user->save(false);
            $profile->save(false);

            // MQTT – paciente atualizado
            Yii::$app->mqtt->publish(
                "user/atualizado/{$user->id}",
                json_encode([
                    'evento'   => 'user_atualizado',
                    'user_id'  => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'nome'     => $profile->nome,
                    'hora'     => date('Y-m-d H:i:s'),
                ])
            );

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

    // GET /api/paciente/perfil
    public function actionPerfil()
    {
        $userId = Yii::$app->user->id;

        $perfil = UserProfile::find()
            ->where(['user_id' => $userId])
            ->asArray()
            ->one();

        if (!$perfil) {
            throw new NotFoundHttpException("Perfil do paciente não encontrado.");
        }

        return $perfil;
    }
}
