<?php
namespace backend\modules\api\controllers;

use Yii;
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

        // ✅ força saída em JSON mesmo se pedirem HTML
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // ✅ autenticação via token ?auth_key=XYZ
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'auth_key',
        ];

        return $behaviors;
    }

    // ✅ Listar todas as pulseiras (GET /api/pulseira)
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $pulseiras = $modelClass::find()
            ->with(['userprofile', 'triagem'])
            ->asArray()
            ->all();

        return [
            'status' => 'success',
            'total' => count($pulseiras),
            'data' => $pulseiras,
        ];
    }

    // ✅ Ver pulseira específica (GET /api/pulseira/{id})
    public function actionView($id)
    {
        $pulseira = \common\models\Pulseira::find()
            ->with(['userprofile', 'triagem'])
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

    // ✅ Criar uma nova pulseira (POST /api/pulseira/create)
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $pulseira = new \common\models\Pulseira();
        $pulseira->load($data, '');

        if ($pulseira->save()) {
            return [
                'status' => 'success',
                'message' => 'Pulseira criada com sucesso!',
                'data' => $pulseira,
            ];
        }

        Yii::$app->response->statusCode = 400;
        return [
            'status' => 'error',
            'errors' => $pulseira->getErrors(),
        ];
    }

    // ✅ Filtro por cor/prioridade (GET /api/pulseira/prioridade?cor=vermelho)
    public function actionPrioridade($cor)
    {
        $modelClass = $this->modelClass;
        $pulseiras = $modelClass::find()
            ->where(['prioridade' => $cor])
            ->with(['userprofile', 'triagem'])
            ->asArray()
            ->all();

        if (empty($pulseiras)) {
            throw new NotFoundHttpException("Nenhuma pulseira encontrada com a cor '{$cor}'.");
        }

        return [
            'status' => 'success',
            'cor' => $cor,
            'total' => count($pulseiras),
            'data' => $pulseiras,
        ];
    }
    public function actionPendentes($auth_key = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$auth_key) {
            return ['status' => 'error', 'message' => 'Auth key não fornecida.', 'data' => []];
        }

        $user = \common\models\User::findOne(['auth_key' => $auth_key]);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Acesso negado. Auth key inválida.', 'data' => []];
        }

        try {
            // 🔹 Busca pulseiras cuja prioridade esteja vazia OU seja 'Pendente'
            $pulseiras = \common\models\Pulseira::find()
                ->alias('p')
                ->joinWith('userprofile up')
                ->where(['or',
                    ['p.prioridade' => 'Pendente'],
                    ['p.prioridade' => '']
                ])
                ->orderBy(['p.tempoentrada' => SORT_DESC])
                ->asArray()
                ->all();

            if (!$pulseiras) {
                return [
                    'status' => 'success',
                    'message' => 'Nenhuma pulseira pendente encontrada.',
                    'data' => []
                ];
            }

            // 🔹 Monta o resultado
            $data = [];
            foreach ($pulseiras as $p) {
                $data[] = [
                    'id' => $p['id'],
                    'codigo' => $p['codigo'],
                    'nome' => $p['userprofile']['nome'] ?? 'Desconhecido',
                    'sns' => $p['userprofile']['sns'] ?? 'N/A',
                    'prioridade' => $p['prioridade'] ?: 'Pendente',
                    'hora' => date('H:i', strtotime($p['tempoentrada'])),
                    'status' => $p['status'],
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Pulseiras pendentes encontradas.',
                'data' => $data
            ];

        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao obter pulseiras pendentes: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

}
