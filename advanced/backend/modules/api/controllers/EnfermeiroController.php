<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\User;
use common\models\UserProfile;

class EnfermeiroController extends ActiveController
{
    public $modelClass = 'common\models\UserProfile';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $b = parent::behaviors();
        unset($b['authenticator']);

        // Define formato JSON para tudo
        $b['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_JSON;

        $b['authenticator'] = [
            'class'      => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $b;
    }

    // --- CORREÇÃO IMPORTANTE: Autorizar POST no update ---
    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['update'] = ['POST', 'PUT', 'PATCH'];
        return $verbs;
    }

    public function actions()
    {
        $a = parent::actions();
        // Removemos ações padrão para personalizar
        unset($a['index'], $a['view'], $a['create'], $a['update'], $a['delete']);
        return $a;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // Admin pode tudo
        if (Yii::$app->user->can('admin')) {
            return;
        }

        // Utilizador normal só pode ver/editar o seu próprio perfil
        if ($action === 'view' || $action === 'perfil' || $action === 'update') {
            if ($model && $model->user_id == Yii::$app->user->id) {
                return;
            }
            // Se for ação 'perfil' sem modelo, passa (validado na função)
            if ($action === 'perfil' && !$model) return;

            throw new ForbiddenHttpException("Não tem permissão para alterar este perfil.");
        }
    }

    // GET /api/enfermeiro/perfil
    public function actionPerfil()
    {
        $userId = Yii::$app->user->id;

        $perfil = UserProfile::find()
            ->where(['user_id' => $userId])
            ->asArray()
            ->one();

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
        // 1. Encontrar o perfil pelo user_id (que vem na URL)
        $model = UserProfile::findOne(['user_id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException("Perfil não encontrado para o utilizador $id");
        }

        $this->checkAccess('update', $model);

        // 2. Receber dados (suporta formato "Enfermeiro[nome]" do Android)
        $dados = Yii::$app->request->post('Enfermeiro');
        if (!$dados) {
            $dados = Yii::$app->request->getBodyParams();
        }

        // 3. Atualizar campos do Perfil
        if (isset($dados['nome']))     $model->nome     = $dados['nome'];
        if (isset($dados['telefone'])) $model->telefone = $dados['telefone'];
        if (isset($dados['nif']))      $model->nif      = $dados['nif'];
        if (isset($dados['sns']))      $model->sns      = $dados['sns'];
        if (isset($dados['morada']))   $model->morada   = $dados['morada'];
        if (isset($dados['datanascimento'])) $model->datanascimento = $dados['datanascimento'];

        // 4. Atualizar Email na tabela User
        if (isset($dados['email'])) {
            $user = User::findOne($model->user_id);
            if ($user) {
                $user->email = $dados['email'];
                $user->save(false); // Save rápido sem validações complexas
            }
        }

        // 5. Guardar e Notificar MQTT
        if ($model->save()) {

            // --- NOTIFICAÇÃO MQTT ---
            if (isset(Yii::$app->mqtt)) {
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
            }
            // ------------------------

            return $model;
        } else {
            Yii::$app->response->statusCode = 422;
            return $model->getErrors();
        }
    }
}