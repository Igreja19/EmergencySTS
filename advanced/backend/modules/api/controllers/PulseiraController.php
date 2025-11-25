<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\QueryParamAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\CompositeAuth; 
use common\models\Pulseira;
use common\models\Triagem;
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
        
        // CONFIGURAÇÃO DE AUTENTICAÇÃO CORRIGIDA
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class, 
                [
                    'class' => QueryParamAuth::class, 
                    'tokenParam' => 'access-token',   
                ],
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        // Desativar as ações padrão para usarmos as nossas personalizadas
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    // LISTAR (GET)
    public function actionIndex()
    {
        $user = Yii::$app->user;
        $request = Yii::$app->request;

        if ($user->can('enfermeiro') || $user->can('medico') || $user->can('admin')) {
            // Use o namespace completo para garantir que não há erro de importação
            $query = \common\models\Pulseira::find();
            if ($status = $request->get('status')) {
                $query->where(['status' => $status]);
            }
            $pulseiras = $query->orderBy(['tempoentrada' => SORT_ASC])->all();
        } else {
            $profile = \common\models\UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) throw new NotFoundHttpException("Perfil não encontrado.");

            $pulseiras = \common\models\Pulseira::find()
                ->where(['userprofile_id' => $profile->id])
                ->orderBy(['tempoentrada' => SORT_DESC])
                ->all();
        }

        // Formatar JSON com SEGURANÇA TOTAL
        $data = [];
        foreach ($pulseiras as $pulseira) {
            
            $triagemId = null;
            
            // Só tenta buscar triagem se a classe existir (evita crash se faltar ficheiro)
            if (class_exists('\common\models\Triagem')) {
                // Tenta via relação primeiro (mais eficiente e seguro)
                if ($pulseira->getTriagem()->exists()) {
                     $triagemId = $pulseira->triagem->id;
                } 
                // Fallback: Tenta buscar manualmente se a relação falhar
                else {
                    $t = \common\models\Triagem::findOne(['pulseira_id' => $pulseira->id]);
                    if ($t) $triagemId = $t->id;
                }
            }

            // Nome do Paciente (com proteção contra nulos)
            $nomePaciente = 'Desconhecido';
            $snsPaciente = 'N/A';
            
            // Verifica se a relação userprofile existe e não é nula
            if (!empty($pulseira->userprofile)) {
                $nomePaciente = $pulseira->userprofile->nome;
                $snsPaciente = $pulseira->userprofile->sns;
            }

            $data[] = [
                'id'            => $pulseira->id,
                'codigo'        => $pulseira->codigo,
                'status'        => $pulseira->status,
                'prioridade'    => $pulseira->prioridade,
                'tempoentrada'  => $pulseira->tempoentrada,
                'paciente'      => [
                    'nome' => $nomePaciente,
                    'sns'  => $snsPaciente,
                ],
                'triagem_id'    => $triagemId,
            ];
        }

        return ['status' => 'success', 'total' => count($data), 'data' => $data];
    }


    // VER UMA (GET ID)
    public function actionView($id)
    {
        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) throw new NotFoundHttpException("Pulseira não encontrada.");

        $user = Yii::$app->user;
        if (!$user->can('enfermeiro') && !$user->can('medico') && !$user->can('admin')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile || $pulseira->userprofile_id != $profile->id) {
                throw new ForbiddenHttpException("Acesso negado.");
            }
        }

        return ['status' => 'success', 'data' => $pulseira];
    }


    // ATUALIZAR (PUT ID)
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais de saúde podem alterar pulseiras.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $data = Yii::$app->request->getBodyParams();
        if (empty($data)) {
            $data = Yii::$app->request->post();
        }
        
        if (isset($data['prioridade'])) {
            $pulseira->prioridade = $data['prioridade'];
        }
        if (isset($data['status'])) {
            $pulseira->status = $data['status'];
        }

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

    // APAGAR (DELETE)
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