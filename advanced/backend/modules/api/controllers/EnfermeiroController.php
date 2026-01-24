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

    public function actions()
    {
        $actions = parent::actions();
        // Removemos as ações padrão para controlar tudo manualmente
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionPerfil()
    {
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas enfermeiros podem aceder a esta área.");
        }

        $userId = Yii::$app->user->id;

        $perfil = UserProfile::find()
            ->where(['user_id' => $userId])
            ->asArray()
            ->one();

        if (!$perfil) {
            throw new NotFoundHttpException("Perfil de enfermeiro não encontrado.");
        }

        $user = User::findOne($userId);
        if ($user) {
            $perfil['email'] = $user->email;
            $perfil['username'] = $user->username;
        }

        return $perfil;
    }

    public function actionUpdate($id)
    {
        $model = UserProfile::findOne(['user_id' => $id]);

        if (!$model) {
            $model = UserProfile::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException("Perfil não encontrado (ID: $id).");
        }

        if (!Yii::$app->user->can('admin')) {
            if ($model->user_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException("Não tem permissão para alterar dados de outro enfermeiro.");
            }
        }

        $dados = Yii::$app->request->getBodyParams();

        if (isset($dados['Enfermeiro'])) {
            $dados = array_merge($dados, $dados['Enfermeiro']);
        }

        if (isset($dados['nome']))     $model->nome     = $dados['nome'];
        if (isset($dados['telefone'])) $model->telefone = $dados['telefone'];
        if (isset($dados['nif']))      $model->nif      = $dados['nif'];
        if (isset($dados['sns']))      $model->sns      = $dados['sns'];
        if (isset($dados['morada']))   $model->morada   = $dados['morada'];
        if (isset($dados['datanascimento'])) $model->datanascimento = $dados['datanascimento'];

        if (isset($dados['email'])) {
            $user = User::findOne($model->user_id);
            if ($user) {
                $user->email = $dados['email'];
                $user->save(false);
            }
        }

        if ($model->save()) {
            $this->safeMqttPublish("user/atualizado/{$model->user_id}", [
                'evento'   => 'user_atualizado',
                'user_id'  => $model->user_id,
                'role'     => 'enfermeiro',
                'nome'     => $model->nome,
                'hora'     => date('Y-m-d H:i:s'),
            ]);

            $response = $model->toArray();
            $response['email'] = isset($user) ? $user->email : '';
            return $response;
        }

        Yii::$app->response->statusCode = 422;
        return $model->getErrors();
    }

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