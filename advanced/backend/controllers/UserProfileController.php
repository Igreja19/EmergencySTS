<?php

namespace backend\controllers;

use Yii;
use common\models\UserProfile;
use common\models\UserProfileSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
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

    public function actionIndex()
    {
        $searchModel = new UserProfileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new UserProfile();

        // Definir user_id automaticamente se estiver autenticado
        if (Yii::$app->user->identity) {
            $model->user_id = Yii::$app->user->id;
        }

        if ($model->load(Yii::$app->request->post())) {

            // ⚠️ Criar utilizador base se não existir
            $user = \common\models\User::findOne(['email' => $model->email]);
            if (!$user) {
                $user = new \common\models\User();
                $user->username = $model->email;
                $user->email = $model->email;
                $user->setPassword('123456'); // password padrão
                $user->generateAuthKey();

                if (!$user->save()) {
                    Yii::$app->session->setFlash('error', 'Erro ao criar utilizador base: ' . json_encode($user->getErrors()));
                    return $this->render('create', compact('model'));
                }
            }

            $model->user_id = $user->id;

            // 🔹 Guardar perfil
            if ($model->save(false)) {
                // Atribuir role, se selecionada
                if (!empty($model->role)) {
                    $auth = Yii::$app->authManager;
                    $auth->revokeAll($model->user_id);
                    $role = $auth->getRole($model->role);
                    if ($role) {
                        $auth->assign($role, $model->user_id);
                    }
                }

                Yii::$app->session->setFlash('success', 'Utilizador criado com sucesso!');
                return $this->redirect(['index']); // redireciona para a lista
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao guardar o perfil: ' . json_encode($model->getErrors()));
            }
        }

        return $this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldEmail = $model->email;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save(false)) {
                // Atualizar email do User base
                if ($oldEmail !== $model->email && $model->user_id) {
                    if ($user = \common\models\User::findOne($model->user_id)) {
                        $user->email = $model->email;
                        $user->username = $model->email;
                        $user->save(false);
                    }
                }

                // Atualizar role
                if (!empty($model->role)) {
                    $auth = Yii::$app->authManager;
                    $auth->revokeAll($model->user_id);
                    $role = $auth->getRole($model->role);
                    if ($role) {
                        $auth->assign($role, $model->user_id);
                    }
                }

                Yii::$app->session->setFlash('success', 'Perfil atualizado com sucesso!');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao atualizar perfil.');
            }
        }

        return $this->render('update', compact('model'));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $userId = $model->user_id;

        $model->delete();

        if ($userId) {
            Yii::$app->authManager->revokeAll($userId);
        }

        Yii::$app->session->setFlash('success', 'Utilizador eliminado.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = UserProfile::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('O perfil solicitado não existe.');
    }
}
