<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use common\models\User;
use common\models\UserProfile;

class PacienteController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function verbs()
    {
        return [
            'update' => ['POST', 'PUT', 'PATCH'],
            'perfil' => ['GET']
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        // Desativar a ação de update padrão do Yii para usares a tua personalizada
        unset($actions['update']);
        return $actions;
    }

    /**
     * GET /api/paciente/perfil?id=XX
     */
    public function actionPerfil($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            return ["status" => false, "message" => "Utilizador não encontrado."];
        }

        $perfil = UserProfile::findOne(['user_id' => $id]);
        if (!$perfil) {
            return ["status" => false, "message" => "Perfil não encontrado."];
        }

        return [
            "status" => true,
            "data" => [
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email, // Lê do USER

                "nome" => $perfil->nome,
                "morada" => $perfil->morada,
                "nif" => $perfil->nif,
                "sns" => $perfil->sns,
                "datanascimento" => $perfil->datanascimento,
                "genero" => $perfil->genero,
                "telefone" => $perfil->telefone,
            ]
        ];
    }

    /**
     * POST /api/paciente/update
     * Body: { id: 1, nome: "...", email: "...", ... }
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['status' => false, 'message' => 'ID em falta'];
        }

        $user = User::findOne($id);
        $profile = UserProfile::findOne(['user_id' => $id]);

        if (!$user || !$profile) {
            return ['status' => false, 'message' => 'Utilizador não encontrado'];
        }

        // USER
        $user->username = Yii::$app->request->post('username');
        $user->email    = Yii::$app->request->post('email'); // ⚠️ O Email deve ser guardado no USER

        // PROFILE
        $profile->nome = Yii::$app->request->post('nome');
        $profile->telefone = Yii::$app->request->post('telefone');
        $profile->datanascimento = Yii::$app->request->post('datanascimento');
        $profile->genero = Yii::$app->request->post('genero');
        $profile->sns = Yii::$app->request->post('sns');
        $profile->nif = Yii::$app->request->post('nif');
        $profile->morada = Yii::$app->request->post('morada');

        // VALIDAR E GUARDAR
        if ($user->validate() && $profile->validate()) {
            $user->save(false);
            $profile->save(false);

            return [
                'status' => true,
                'message' => 'Dados atualizados com sucesso',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,

                    'nome' => $profile->nome,
                    'genero' => $profile->genero,
                    'datanascimento' => $profile->datanascimento,
                    'telefone' => $profile->telefone,
                    'sns' => $profile->sns,
                    'nif' => $profile->nif,
                    'morada' => $profile->morada
                ]
            ];
        }

        return [
            'status' => false,
            'message' => 'Erro ao validar dados',
            'errors' => [
                'user' => $user->errors,
                'profile' => $profile->errors
            ]
        ];
    }
}