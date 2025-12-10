<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;

use common\models\Triagem;
use common\models\UserProfile;
use common\models\Pulseira;

class TriagemController extends ActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        // output default JSON
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Auth por ?auth_key=
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset(
            $actions['index'],
            $actions['view'],
            $actions['create'],
            $actions['update'],
            $actions['delete']
        );

        return $actions;
    }

    //  LISTAR TODAS AS TRIAGENS (APLICAÇÃO PROFISSIONAL)

    public function actionIndex()
    {
        $user = Yii::$app->user;

        $query = Triagem::find()
            ->with(['userprofile', 'pulseira'])
            ->orderBy(['datatriagem' => SORT_DESC]);

        // Filtro por pulseira se vier do GET
        if ($pulseiraId = Yii::$app->request->get('pulseira_id')) {
            $query->andWhere(['pulseira_id' => $pulseiraId]);
        }

        // Paciente: só vê as próprias triagens
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if (!$profile) {
                throw new NotFoundHttpException("Perfil não encontrado.");
            }
            $query->andWhere(['userprofile_id' => $profile->id]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    //  HISTÓRICO DE TRIAGENS APENAS DE CONSULTAS ENCERRADAS

    public function actionHistorico()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Triagem::find()
            ->joinWith(['consulta']) // junta tabela consulta
            ->where(['consulta.estado' => 'encerrada'])
            ->with(['pulseira', 'userprofile'])
            ->orderBy(['triagem.datatriagem' => SORT_DESC]);

        // Paciente vê só as suas triagens
        $user = Yii::$app->user;
        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            $query->andWhere(['triagem.userprofile_id' => $profile->id]);
        }

        return $query->all();
    }

    //  VIEW

    public function actionView($id)
    {
        $triagem = Triagem::find()
            ->with(['userprofile', 'pulseira'])
            ->where(['id' => $id])
            ->one();

        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        $user = Yii::$app->user;

        if (!$user->can('admin') && !$user->can('medico') && !$user->can('enfermeiro')) {
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            if ($triagem->userprofile_id != $profile->id) {
                throw new ForbiddenHttpException("Não tem permissão.");
            }
        }

        return $triagem;
    }

    //  CREATE

    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $user = Yii::$app->user;

        $profile = UserProfile::findOne(['user_id' => $user->id]);
        if (!$profile) {
            throw new BadRequestHttpException("Utilizador sem perfil associado.");
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Criar Triagem
            $triagem = new Triagem();
            $triagem->load($data, '');
            $triagem->userprofile_id = $profile->id;
            $triagem->datatriagem = date('Y-m-d H:i:s');

            if (!$triagem->save()) {
                throw new \Exception(json_encode($triagem->errors));
            }

            // Criar Pulseira
            $pulseira = new Pulseira([
                'userprofile_id' => $profile->id,
                'codigo' => 'P-' . strtoupper(substr(uniqid(), -5)),
                'prioridade' => 'Pendente',
                'status' => 'Em espera',
                'tempoentrada' => date('Y-m-d H:i:s')
            ]);

            if (!$pulseira->save()) {
                throw new \Exception(json_encode($pulseira->errors));
            }

            // Associar pulseira
            $triagem->pulseira_id = $pulseira->id;
            $triagem->save();

            $transaction->commit();

            return [
                'status' => 'success',
                'triagem' => $triagem,
                'pulseira' => $pulseira
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 422;

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    //  UPDATE

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('enfermeiro') &&
            !Yii::$app->user->can('medico') &&
            !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException("Sem permissão.");
        }

        $triagem = Triagem::findOne($id);
        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        $triagem->load(Yii::$app->request->post(), '');
        $triagem->save();

        return $triagem;
    }

    //  DELETE

    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('admin') && !Yii::$app->user->can('enfermeiro')) {
            throw new ForbiddenHttpException("Sem permissão.");
        }

        $triagem = Triagem::findOne($id);
        if (!$triagem) {
            throw new NotFoundHttpException("Triagem não encontrada.");
        }

        if ($triagem->pulseira_id) {
            Pulseira::findOne($triagem->pulseira_id)->delete();
        }

        $triagem->delete();

        return ['status' => 'success', 'message' => 'Triagem apagada.'];
    }
}
