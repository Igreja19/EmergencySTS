<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;

use common\models\Pulseira;
use common\models\UserProfile;

class PulseiraController extends ActiveController
{
    public $modelClass = 'common\models\Pulseira';
    public $enableCsrfValidation = false;

    // Envelope "data": [...]
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

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

    // GET /api/pulseira
    public function actionIndex()
    {
        $user = Yii::$app->user;
        $query = Pulseira::find();

        // filtros
        $status = Yii::$app->request->get('status');
        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $prioridade = Yii::$app->request->get('prioridade');
        if ($prioridade) {
            $query->andWhere(['prioridade' => $prioridade]);
        }

        // permissões
        if ($user->can('admin') || $user->can('medico') || $user->can('enfermeiro')) {
            // vê tudo
        } else {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }
            $query->andWhere(['userprofile_id' => $profile->id]);
        }

        $query->orderBy(['tempoentrada' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    // GET /api/pulseira/{id}
    public function actionView($id)
    {
        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $user = Yii::$app->user;
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile || $pulseira->userprofile_id != $profile->id) {
                throw new ForbiddenHttpException("Sem permissão.");
            }
        }

        return $pulseira;
    }

    // PUT /api/pulseira/{id}
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('enfermeiro') &&
            !Yii::$app->user->can('medico') &&
            !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais de saúde.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $data = Yii::$app->request->post();
        $pulseira->load($data, '');

        if ($pulseira->save()) {

            // MQTT – pulseira atualizada
            Yii::$app->mqtt->publish(
                "pulseira/atualizada/{$pulseira->id}",
                json_encode([
                    'evento'        => 'pulseira_atualizada',
                    'pulseira_id'   => $pulseira->id,
                    'prioridade'    => $pulseira->prioridade,
                    'status'        => $pulseira->status,
                    'userprofile_id'=> $pulseira->userprofile_id,
                    'hora'          => date('Y-m-d H:i:s'),
                ])
            );

            return $pulseira;
        }

        return [
            'status' => 'error',
            'errors' => $pulseira->getErrors(),
        ];
    }

    // DELETE /api/pulseira/{id}
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas administradores.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Não encontrada.");
        }

        $pulseira->delete();

        // MQTT – pulseira apagada
        Yii::$app->mqtt->publish(
            "pulseira/apagada/{$id}",
            json_encode([
                'evento'      => 'pulseira_apagada',
                'pulseira_id' => $id,
                'hora'        => date('Y-m-d H:i:s'),
            ])
        );

        return ['status' => 'success'];
    }
}
