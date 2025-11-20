<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
// ⬇️ ESTES SÃO ESSENCIAIS PARA EVITAR ERRO 500 ⬇️
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\auth\QueryParamAuth;
// ⬇️ MODELOS IMPORTADOS ⬇️
use common\models\Pulseira;
use common\models\UserProfile;

class PulseiraController extends ActiveController
{
    public $modelClass = 'common\models\Pulseira';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    // ----------------------------------------------------------------
    // LISTAR (GET)
    // ----------------------------------------------------------------
    public function actionIndex()
    {
        $user = Yii::$app->user;
        $request = Yii::$app->request;

        if ($user->can('enfermeiro') || $user->can('medico') || $user->can('admin')) {
            $query = Pulseira::find();
            if ($status = $request->get('status')) {
                $query->where(['status' => $status]);
            }
            $pulseiras = $query->orderBy(['tempoentrada' => SORT_ASC])->all();
        } else {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) throw new NotFoundHttpException("Perfil não encontrado.");

            $pulseiras = Pulseira::find()
                ->where(['userprofile_id' => $profile->id])
                ->orderBy(['tempoentrada' => SORT_DESC])
                ->all();
        }

        // Formatar JSON
        $data = [];
        foreach ($pulseiras as $pulseira) {
            // Tenta obter a triagem com segurança
            $triagemId = null;
            $triagem = \common\models\Triagem::findOne(['pulseira_id' => $pulseira->id]);
            if ($triagem) {
                $triagemId = $triagem->id;
            }

            $data[] = [
                'pulseira_id' => $pulseira->id,
                'codigo'      => $pulseira->codigo,
                'status'      => $pulseira->status,
                'prioridade'  => $pulseira->prioridade,
                'tempoentrada'=> $pulseira->tempoentrada,
                'paciente'    => [
                    'nome' => $pulseira->userprofile ? $pulseira->userprofile->nome : 'Desconhecido',
                    'sns'  => $pulseira->userprofile ? $pulseira->userprofile->sns : 'N/A',
                ],
                'triagem_id'  => $triagemId,
            ];
        }

        return ['status' => 'success', 'total' => count($data), 'data' => $data];
    }

    // ----------------------------------------------------------------
    // VER UMA (GET ID)
    // ----------------------------------------------------------------
    public function actionView($id)
    {
        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) throw new NotFoundHttpException("Pulseira não encontrada.");

        // Segurança básica
        $user = Yii::$app->user;
        if (!$user->can('enfermeiro') && !$user->can('medico') && !$user->can('admin')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile || $pulseira->userprofile_id != $profile->id) {
                throw new ForbiddenHttpException("Acesso negado.");
            }
        }

        return ['status' => 'success', 'data' => $pulseira];
    }

    // ----------------------------------------------------------------
    // ATUALIZAR (PUT ID) - AQUI ESTAVA O ERRO
    // ----------------------------------------------------------------
    public function actionUpdate($id)
    {
        // 1. Permissão
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais de saúde podem alterar pulseiras.");
        }

        // 2. Encontrar
        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        // 3. Ler Dados (CORREÇÃO: Usar getBodyParams para PUT)
        $data = Yii::$app->request->getBodyParams();

        // Fallback se o JSON falhar
        if (empty($data)) {
            $data = Yii::$app->request->post();
        }

        // 4. Atualizar
        if (isset($data['prioridade'])) {
            $pulseira->prioridade = $data['prioridade'];
        }
        if (isset($data['status'])) {
            $pulseira->status = $data['status'];
        }

        // 5. Guardar
        if ($pulseira->save()) {
            return [
                'status' => 'success',
                'message' => 'Pulseira atualizada.',
                'data' => $pulseira
            ];
        }

        Yii::$app->response->statusCode = 422;
        return ['status' => 'error', 'errors' => $pulseira->getErrors()];
    }

    // ----------------------------------------------------------------
    // APAGAR (DELETE)
    // ----------------------------------------------------------------
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores.");
        }
        $pulseira = Pulseira::findOne($id);
        if ($pulseira) {
            $pulseira->delete();
            return ['status' => 'success'];
        }
        throw new NotFoundHttpException("Não encontrada.");
    }
}