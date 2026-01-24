<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use common\models\Triagem;
use common\models\Pulseira;

class TriagemController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['paciente'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Página inicial da triagem
     */
    public function actionIndex()
    {
        $podeCriarTriagem = true;

        if (!Yii::$app->user->isGuest) {
            $userProfileId = Yii::$app->user->identity->userprofile->id ?? null;

            if ($userProfileId) {
                $ultimaPulseira = Pulseira::find()
                    ->where(['userprofile_id' => $userProfileId])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                if ($ultimaPulseira) {
                    $statusLimpo = strtolower(trim($ultimaPulseira->status));
                    
                    //  Lista de estados permitidos (tudo em minúsculas)
                    $estadosFinais = ['finalizado', 'atendido', 'cancelado', 'concluido', 'concluída'];

                    //  Se NÃO estiver na lista, bloqueia
                    if (!in_array($statusLimpo, $estadosFinais)) {
                        $podeCriarTriagem = false;
                    }
                }
            }
        }

        return $this->render('index', [
            'podeCriarTriagem' => $podeCriarTriagem,
        ]);
    }

    /**
     * Formulário clínico (criação de triagem)
     */
    public function actionFormulario()
    {
        $model = new Triagem();

        if (!Yii::$app->user->isGuest) {
            $model->userprofile_id = Yii::$app->user->identity->userprofile->id ?? null;
        }

        $ultimaPulseira = Pulseira::find()
            ->where(['userprofile_id' => $model->userprofile_id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($ultimaPulseira) {
             $statusLimpo = strtolower(trim($ultimaPulseira->status));
             
             $estadosFinais = ['finalizado', 'atendido', 'cancelado', 'concluido', 'concluída'];
             
             if (!in_array($statusLimpo, $estadosFinais)) {
                
                $estadoOriginal = $ultimaPulseira->status ?: "Desconhecido";
                Yii::$app->session->setFlash('warning', "Já tem uma pulseira ativa (Estado: $estadoOriginal). Aguarde a conclusão.");
                
                return $this->redirect(['site/index']);
             }
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            $model->datatriagem = date('Y-m-d H:i:s');

            $pulseira = new Pulseira();
            $pulseira->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
            $pulseira->prioridade = 'Pendente';
            $pulseira->tempoentrada = date('Y-m-d H:i:s');
            $pulseira->status = 'Em espera'; 
            $pulseira->userprofile_id = $model->userprofile_id;
            
            if (!$pulseira->save(false)) {
                 Yii::$app->session->setFlash('error', 'Erro ao gerar pulseira. Tente novamente.');
                 return $this->render('formulario', ['model' => $model]);
            }

            $model->pulseira_id = $pulseira->id;

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Formulário clínico criado com sucesso!');
                return $this->redirect(['pulseira/index']);
            }
            Yii::$app->session->setFlash('error', 'Erro ao guardar os dados da triagem.');
        }

        return $this->render('formulario', [
            'model' => $model,
        ]);
    }
}