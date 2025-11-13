<?php

namespace frontend\controllers;

use common\models\UserProfile;
use common\models\UserProfileSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserProfileController implements the CRUD actions for UserProfile model.
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
     * Lists all UserProfile models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserProfileSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserProfile model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserProfile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($user_id = null)
    {
        // 1️⃣ Verificar se o utilizador já tem perfil
        if (!Yii::$app->user->isGuest) {
            $profile = UserProfile::find()->where(['user_id' => Yii::$app->user->id])->one();
            if ($profile !== null) {
                return $this->redirect(['triagem/index']);
            }
        }

        $model = new UserProfile();

        // 2️⃣ Preencher user_id (via GET ou sessão)
        if ($user_id !== null) {
            $model->user_id = $user_id;
        } elseif (!Yii::$app->user->isGuest) {
            $model->user_id = Yii::$app->user->identity->id;
        }

        // 3️⃣ POST → load + save
        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                return $this->redirect(['triagem/index']);
            }

            Yii::error($model->errors, 'userprofile_save_errors');
        }

        // 4️⃣ Mostrar formulário
        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing UserProfile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserProfile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Guarda o ID do utilizador logado
        $currentUserId = Yii::$app->user->id;

        // Apaga o perfil
        $model->delete();

        // Se o perfil apagado pertencer ao utilizador autenticado, faz logout
        if ($model->user_id == $currentUserId) {
            $user = \common\models\User::findOne($model->user_id);
            if ($user) {
                $user->delete();
            }
            Yii::$app->user->logout();
            return $this->redirect(['site/index']);
        }

        // Caso contrário, redireciona normalmente
        return $this->redirect(['index']);
    }

    /**
     * Finds the UserProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return UserProfile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserProfile::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
