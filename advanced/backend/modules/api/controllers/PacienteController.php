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

        // Resposta JSON
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    /**
     * GET /backend/web/api/paciente/perfil?id=XX
     */
    public function actionPerfil($id)
    {
        // User
        $user = User::findOne($id);
        if (!$user) {
            return [
                "status" => false,
                "message" => "Utilizador não encontrado."
            ];
        }

        // UserProfile
        $perfil = UserProfile::findOne(['user_id' => $id]);
        if (!$perfil) {
            return [
                "status" => false,
                "message" => "Perfil não encontrado."
            ];
        }

        return [
            "status" => true,
            "data" => [
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,

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

    public function verbs()
    {
        return [
            'update' => ['PUT', 'PATCH'], // <– Yii REQUER isto
            'perfil' => ['GET']
        ];
    }

    public function actionUpdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['status' => false, 'message' => 'ID em falta'];
        }

        $user = \common\models\User::findOne($id);
        $profile = \common\models\UserProfile::find()->where(['user_id' => $id])->one();

        if (!$user || !$profile) {
            return ['status' => false, 'message' => 'Utilizador não encontrado'];
        }

        // ----------- CAMPOS DO USER -----------
        $user->username = Yii::$app->request->post('username');
        // Se quiseres validar email no user, podes pôr aqui,
        // mas tu disseste que email está no profile.

        // ----------- CAMPOS DO PROFILE -----------
        $profile->nome = Yii::$app->request->post('nome');
        $profile->telefone = Yii::$app->request->post('telefone');
        $profile->datanascimento = Yii::$app->request->post('datanascimento');
        $profile->genero = Yii::$app->request->post('genero');
        $profile->sns = Yii::$app->request->post('sns');
        $profile->nif = Yii::$app->request->post('nif');
        $profile->morada = Yii::$app->request->post('morada');
        $profile->email = Yii::$app->request->post('email'); // 👈 aqui está correto

        // ----------- GUARDAR -----------
        if ($user->save(false) && $profile->save(false)) {

            return [
                'status' => true,
                'message' => 'Dados atualizados com sucesso',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,

                    // PROFILE DATA
                    'nome' => $profile->nome,
                    'email' => $profile->email,
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
            'message' => 'Erro ao atualizar',
            'errors' => [
                'user' => $user->errors,
                'profile' => $profile->errors
            ]
        ];
    }

}
