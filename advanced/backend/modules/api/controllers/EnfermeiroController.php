<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use backend\modules\api\controllers\BaseActiveController;

use common\models\User;
use common\models\UserProfile;

class EnfermeiroController extends BaseActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    // NOTA: behaviors() removido porque herda do BaseActiveController

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['update'] = ['POST', 'PUT', 'PATCH']; // Permite POST para update
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
        // O BaseActiveController já bloqueou os Pacientes.
        // Aqui garantimos que um Enfermeiro não edita outro Enfermeiro.

        if (Yii::$app->user->can('admin')) {
            return;
        }

        if ($action === 'update' || $action === 'perfil') {
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            if ($action === 'perfil' && !$model) return;

            throw new ForbiddenHttpException("Não tem permissão para alterar este perfil.");
        }
    }

    // GET /api/enfermeiro/perfil
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

    // POST /api/enfermeiro/{id}
    public function actionUpdate($id)
    {
        // 1. Procura robusta
        $model = UserProfile::findOne(['user_id' => $id]);
        if (!$model) {
            $model = UserProfile::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException("Perfil não encontrado.");
        }

        $this->checkAccess('update', $model);

        // 2. Receber dados
        $dados = Yii::$app->request->getBodyParams();

        // Suporte legacy
        if (isset($dados['Enfermeiro'])) {
            $dados = $dados['Enfermeiro'];
        }

        // 3. Atualizar campos
        if (isset($dados['nome']))     $model->nome     = $dados['nome'];
        if (isset($dados['telefone'])) $model->telefone = $dados['telefone'];
        if (isset($dados['nif']))      $model->nif      = $dados['nif'];
        if (isset($dados['sns']))      $model->sns      = $dados['sns'];
        if (isset($dados['morada']))   $model->morada   = $dados['morada'];
        if (isset($dados['datanascimento'])) $model->datanascimento = $dados['datanascimento'];

        // 4. Atualizar Email
        if (isset($dados['email'])) {
            $user = User::findOne($model->user_id);
            if ($user) {
                $user->email = $dados['email'];
                $user->save(false);
            }
        }

        // 5. Guardar
        if ($model->save()) {

            // MQTT Seguro
            $mqttEnabled = Yii::$app->params['mqtt_enabled'] ?? true;
            if ($mqttEnabled && isset(Yii::$app->mqtt)) {
                try {
                    Yii::$app->mqtt->publish(
                        "user/atualizado/{$model->user_id}",
                        json_encode([
                            'evento'   => 'user_atualizado',
                            'user_id'  => $model->user_id,
                            'role'     => 'enfermeiro',
                            'nome'     => $model->nome,
                            'hora'     => date('Y-m-d H:i:s'),
                        ])
                    );
                } catch (\Exception $e) {
                    Yii::error("Erro MQTT Enfermeiro Update: " . $e->getMessage());
                }
            }

            return $model;
        } else {
            Yii::$app->response->statusCode = 422;
            return $model->getErrors();
        }
    }
}