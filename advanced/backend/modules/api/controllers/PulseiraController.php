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

    // 🔹 CONFIGURAÇÃO DO SERIALIZER (IMPORTANTE!)
    // Isto obriga a API a devolver { "data": [...] } em vez de [...]
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
            // 'tokenParam' => 'auth_key', // <--- REMOVI ISTO!
            // O padrão é 'access-token', que é exatamente o que a tua App Android envia.
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

        // Filtros
        $status = Yii::$app->request->get('status');
        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $prioridade = Yii::$app->request->get('prioridade');
        if ($prioridade) {
            $query->andWhere(['prioridade' => $prioridade]);
        }

        // Permissões
        if ($user->can('admin') || $user->can('medico') || $user->can('enfermeiro')) {
            // Vê tudo
        } else {
            // Paciente vê só as suas
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if ($profile) {
                $query->andWhere(['userprofile_id' => $profile->id]);
            } else {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }
        }

        $query->orderBy(['tempoentrada' => SORT_DESC]);

        // Retorna DataProvider
        // Graças ao 'collectionEnvelope' lá em cima, o Yii vai meter isto dentro de "data": [...]
        // E o expand=triagem vai funcionar automaticamente!
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    // GET /api/pulseira/{id}
    public function actionView($id)
    {
        $pulseira = Pulseira::find()->where(['id' => $id])->one();

        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        // Segurança
        $user = Yii::$app->user;
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if ($pulseira->userprofile_id != $profile->id) {
                throw new ForbiddenHttpException("Sem permissão.");
            }
        }

        return $pulseira; 
    }

    // PUT /api/pulseira/{id}
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('enfermeiro') && !Yii::$app->user->can('medico') && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Apenas profissionais de saúde.");
        }

        $pulseira = Pulseira::findOne($id);
        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira não encontrada.");
        }

        $data = Yii::$app->request->post();
        $pulseira->load($data, '');

        if ($pulseira->save()) {
            return $pulseira;
        }

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