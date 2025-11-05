<?php
namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\auth\QueryParamAuth;

class TriagemController extends ActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // ✅ força o formato JSON
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // ✅ autenticação via auth_key
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    // ✅ Personalizar listagem (GET /api/triagem)
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $triagens = $modelClass::find()->asArray()->all();

        return [
            'status' => 'success',
            'total' => count($triagens),
            'data' => $triagens,
        ];
    }

    // ✅ Ver triagem por ID (GET /api/triagem/{id})
    public function actionView($id)
    {
        $triagem = \common\models\Triagem::find()
            ->asArray()
            ->where(['id' => $id])
            ->one();

        if (!$triagem) {
            throw new NotFoundHttpException("Triagem com ID {$id} não encontrada.");
        }

        return [
            'status' => 'success',
            'data' => $triagem,
        ];
    }
}
