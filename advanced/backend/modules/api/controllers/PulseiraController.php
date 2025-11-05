<?php
namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\auth\QueryParamAuth;

class PulseiraController extends ActiveController
{
    public $modelClass = 'common\models\Pulseira';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // ✅ força JSON
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // ✅ autenticação com auth_key
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    // ✅ Listar todas as pulseiras
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $pulseiras = $modelClass::find()
            ->asArray()
            ->with(['paciente', 'triagem'])
            ->all();

        return [
            'status' => 'success',
            'total' => count($pulseiras),
            'data' => $pulseiras,
        ];
    }

    // ✅ Ver uma pulseira específica
    public function actionView($id)
    {
        $pulseira = \common\models\Pulseira::find()
            ->with(['paciente', 'triagem'])
            ->asArray()
            ->where(['id' => $id])
            ->one();

        if (!$pulseira) {
            throw new NotFoundHttpException("Pulseira com ID {$id} não encontrada.");
        }

        return [
            'status' => 'success',
            'data' => $pulseira,
        ];
    }

    // ✅ Criar uma nova pulseira (POST)
    public function actionCreate()
    {
        $data = \Yii::$app->request->post();
        $pulseira = new \common\models\Pulseira();
        $pulseira->load($data, '');

        if ($pulseira->save()) {
            return [
                'status' => 'success',
                'message' => 'Pulseira criada com sucesso!',
                'data' => $pulseira,
            ];
        }

        return [
            'status' => 'error',
            'errors' => $pulseira->getErrors(),
        ];
    }
}
