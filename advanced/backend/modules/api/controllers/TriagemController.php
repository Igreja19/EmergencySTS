<?php
namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\auth\QueryParamAuth;
use common\models\Triagem;

class TriagemController extends ActiveController
{
    public $modelClass = 'common\models\Triagem';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // ✅ Força saída em JSON mesmo que o cliente peça HTML
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // ✅ Autenticação via parâmetro na URL (?auth_key=XYZ)
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    // ✅ Listar todas as triagens (GET /api/triagem)
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

    // ✅ Ver triagem específica (GET /api/triagem/{id})
    public function actionView($id)
    {
        $triagem = Triagem::find()->asArray()->where(['id' => $id])->one();

        if (!$triagem) {
            throw new NotFoundHttpException("Triagem com ID {$id} não encontrada.");
        }

        return [
            'status' => 'success',
            'data' => $triagem,
        ];
    }

    // ✅ Criar triagem (POST /api/triagem/create)
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $triagem = new Triagem();
        $triagem->load($data, '');

        // 🔹 Validação dos campos obrigatórios
        if (empty($triagem->userprofile_id) || empty($triagem->prioridadeatribuida) || empty($triagem->sintomas)) {
            throw new BadRequestHttpException('Campos obrigatórios: userprofile_id, sintomas, prioridadeatribuida.');
        }

        if ($triagem->save()) {
            return [
                'status' => 'success',
                'message' => 'Triagem criada com sucesso!',
                'data' => $triagem,
            ];
        }

        // 🔹 Caso falhe a validação, devolve erro 400
        Yii::$app->response->statusCode = 400;
        return [
            'status' => 'error',
            'message' => 'Erro ao criar triagem.',
            'errors' => $triagem->getErrors(),
        ];
    }
}
