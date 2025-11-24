<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\auth\QueryParamAuth;
use common\models\Consulta;
use common\models\UserProfile;
use common\models\Triagem;

class ConsultaController extends ActiveController
{
    public $modelClass = 'common\models\Consulta';
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
        // Desligamos as ações padrão para controlar a lógica manualmente
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

   
    //  HISTÓRICO DE CONSULTAS (GET /api/userprofiles/{id}/consultas)
   
    public function actionHistorico($id)
    {

        $profile = UserProfile::findOne($id);
        if (!$profile) {
            throw new NotFoundHttpException("Perfil de utilizador não encontrado.");
        }

        $user = Yii::$app->user;
        if (!$user->can('enfermeiro') && !$user->can('medico') && !$user->can('admin')) {
            // Se for paciente, verifica se é o dono do perfil
            $myProfile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$myProfile || $myProfile->id != $id) {
                throw new ForbiddenHttpException("Não tem permissão para ver o histórico deste paciente.");
            }
        }

        $consultas = Consulta::find()
            ->where(['userprofile_id' => $id])
            ->orderBy(['data_consulta' => SORT_DESC]) 
            ->all();

        $data = [];
        foreach ($consultas as $consulta) {
            $data[] = [
                'id' => $consulta->id,
                'data' => $consulta->data_consulta,
                'estado' => $consulta->estado, 
                'observacoes' => $consulta->observacoes,
                'relatorio_pdf' => $consulta->relatorio_pdf,
            
                'triagem' => $consulta->triagem ? [
                    'queixa' => $consulta->triagem->queixaprincipal,
                    'prioridade' => $consulta->triagem->prioridadeatribuida ?? 'N/A'
                ] : null,
            ];
        }

        return [
            'status' => 'success',
            'total' => count($data),
            'data' => $data
        ];
    }
    
    //  INICIAR CONSULTA (POST /api/consulta)
    public function actionCreate()
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem iniciar consultas.");
        }

        $data = Yii::$app->request->post();
        
    
        if (empty($data['triagem_id'])) {
            throw new BadRequestHttpException("É necessário indicar o 'triagem_id'.");
        }

        $triagem = Triagem::findOne($data['triagem_id']);
        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }


        $consulta = new Consulta();
        $consulta->triagem_id = $triagem->id;
        $consulta->userprofile_id = $triagem->userprofile_id;
        $consulta->data_consulta = date('Y-m-d H:i:s');
        $consulta->estado = 'Em curso'; 
        
        if (isset($data['observacoes'])) {
            $consulta->observacoes = $data['observacoes'];
        }

        if ($consulta->save()) {
        
            if ($triagem->pulseira) {
                $triagem->pulseira->status = 'Em atendimento';
                $triagem->pulseira->save();
            }

            return [
                'status' => 'success',
                'message' => 'Consulta iniciada.',
                'data' => $consulta
            ];
        }

        Yii::$app->response->statusCode = 422;
        return ['status' => 'error', 'errors' => $consulta->getErrors()];
    }

    
    //  ATUALIZAR / ENCERRAR (PUT /api/consulta/{id})
    
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas médicos podem atualizar consultas.");
        }

        $consulta = Consulta::findOne($id);
        if (!$consulta) {
            throw new NotFoundHttpException("Consulta não encontrada.");
        }

        $data = Yii::$app->request->getBodyParams() ?: Yii::$app->request->post();

        // Atualizar campos
        if (isset($data['observacoes'])) {
            $consulta->observacoes = $data['observacoes'];
        }
        if (isset($data['estado'])) {
            $consulta->estado = $data['estado'];
        }

        if ($consulta->save()) {
            
            $debugMsg = "Consulta guardada.";

            // Se a consulta for encerrada, atualizar a pulseira para "Atendido"
            if ($consulta->estado === 'Encerrada') {
                $triagem = Triagem::findOne($consulta->triagem_id);
                
                if ($triagem) {
                    if ($triagem->pulseira) {
                        $triagem->pulseira->status = 'Atendido';
                        if ($triagem->pulseira->save()) {
                            $debugMsg .= " Pulseira ID {$triagem->pulseira->id} atualizada para Atendido.";
                        } else {
                            $debugMsg .= " ERRO ao guardar Pulseira: " . json_encode($triagem->pulseira->getErrors());
                        }
                    } else {
                        $debugMsg .= " AVISO: Triagem {$triagem->id} não tem pulseira associada (pulseira_id nulo ou inválido).";
                    }
                } else {
                    $debugMsg .= " ERRO: Triagem {$consulta->triagem_id} não encontrada.";
                }
            }

            return [
                'status' => 'success',
                'message' => $debugMsg, // A mensagem vai dizer-nos o que aconteceu!
                'data' => $consulta
            ];
        }

        return ['status' => 'error', 'errors' => $consulta->getErrors()];
    }
}