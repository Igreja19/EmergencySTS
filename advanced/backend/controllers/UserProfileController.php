<?php

namespace backend\controllers;

use common\models\Userprofile;
use common\models\UserProfileSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserProfileController implementa as ações CRUD para o modelo Userprofile.
 */
class UserProfileController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lista todos os perfis (admin).
     */
    public function actionIndex()
    {
        $searchModel = new UserProfileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Mostra o perfil do utilizador autenticado (sidebar -> "Perfil").
     */
    public function actionMeuPerfil()
    {
        $userId = Yii::$app->user->id;

        if (!$userId) {
            Yii::$app->session->setFlash('error', 'É necessário iniciar sessão para aceder ao perfil.');
            return $this->redirect(['site/login']);
        }

        $perfil = Userprofile::findOne(['user_id' => $userId]);

        if (!$perfil) {
            Yii::$app->session->setFlash('warning', 'Nenhum perfil encontrado para este utilizador.');
            return $this->redirect(['index']);
        }

        return $this->render('view', [
            'model' => $perfil,
        ]);
    }

    /**
     * Mostra um perfil específico (para admin ou dono do perfil).
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->user_id !== Yii::$app->user->id && !Yii::$app->user->can('admin')) {
            throw new \yii\web\ForbiddenHttpException('Não tem permissão para aceder a este perfil.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Cria um novo perfil e atribui a role manualmente (se selecionada).
     */
    public function actionCreate()
    {
        $model = new Userprofile();
        $auth = Yii::$app->authManager;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            // Se não houver user_id explícito, associa ao user autenticado
            if (!Yii::$app->user->isGuest && empty($model->user_id)) {
                $model->user_id = Yii::$app->user->id;
            }

            // IDs default para evitar erro de FK
            $model->consulta_id = $model->consulta_id ?? 1;
            $model->triagem_id = $model->triagem_id ?? 1;

            if ($model->save()) {

                // ✅ Se uma role for selecionada, atribui manualmente
                if (!empty($model->role)) {
                    $auth->revokeAll($model->user_id); // limpa roles anteriores
                    $role = $auth->getRole($model->role);
                    if ($role) {
                        $auth->assign($role, $model->user_id);
                    }
                }

                Yii::$app->session->setFlash('success', 'Perfil criado com sucesso e função atribuída.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            Yii::$app->session->setFlash('error', 'Erro ao criar o perfil.');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Atualiza um perfil existente e ajusta a role, se necessário.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $auth = Yii::$app->authManager;

        // Role atual do utilizador
        $roles = $auth->getRolesByUser($model->user_id);
        $model->role = !empty($roles) ? array_keys($roles)[0] : null;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                // ✅ Atualiza a role apenas se for escolhida manualmente
                if (!empty($model->role)) {
                    $auth->revokeAll($model->user_id);
                    $role = $auth->getRole($model->role);
                    if ($role) {
                        $auth->assign($role, $model->user_id);
                    }
                }

                Yii::$app->session->setFlash('success', 'Perfil atualizado com sucesso.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar o perfil.');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Elimina um perfil existente.
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->user_id !== Yii::$app->user->id && !Yii::$app->user->can('admin')) {
            throw new \yii\web\ForbiddenHttpException('Não tem permissão para eliminar este perfil.');
        }

        // Remove também roles associadas
        Yii::$app->authManager->revokeAll($model->user_id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Perfil eliminado com sucesso.');
        return $this->redirect(['index']);
    }

    /**
     * Procura o modelo Userprofile pelo ID.
     */
    protected function findModel($id)
    {
        if (($model = Userprofile::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('O perfil solicitado não existe.');
    }
}
