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
                    // 🔹 Acesso completo (CRUD) para admin, médico e enfermeiro
                    [
                        'allow' => true,
                        'roles' => ['admin', 'medico', 'enfermeiro'],
                    ],

                    // 🔹 Ação "meu-perfil" → qualquer utilizador AUTORIZADO (exceto paciente)
                    [
                        'actions' => ['meu-perfil'],
                        'allow' => true,
                        'roles' => ['@'], // autenticados
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

        // Define o user_id automaticamente, se autenticado
        if (Yii::$app->user->identity) {
            $model->user_id = Yii::$app->user->id;
        }

        // 🔹 Obter lista de roles do RBAC
        $roles = Yii::$app->authManager->getRoles();
        $roleOptions = [];
        foreach ($roles as $name => $role) {
            $roleOptions[$name] = ucfirst($name);
        }

        if ($model->load(Yii::$app->request->post())) {

            // Criar utilizador base se não existir
            $user = \common\models\User::findOne(['email' => $model->email]);
            if (!$user) {
                $user = new \common\models\User();
                $user->username = $model->email;
                $user->email = $model->email;
                $user->setPassword('123456'); // senha padrão
                $user->generateAuthKey();

                if (!$user->save()) {
                    Yii::$app->session->setFlash('error', 'Erro ao criar utilizador base: ' . json_encode($user->getErrors()));
                    return $this->render('create', [
                        'model' => $model,
                        'roleOptions' => $roleOptions
                    ]);
                }
            }

            $model->user_id = $user->id;

            // Guardar perfil
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
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao guardar o perfil: ' . json_encode($model->getErrors()));
            }
        }

        return $this->render('create', [
            'model' => $model,
            'roleOptions' => $roleOptions,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldEmail = $model->email;

        // 🔹 Obter lista de roles do RBAC
        $roles = Yii::$app->authManager->getRoles();
        $roleOptions = [];
        foreach ($roles as $name => $role) {
            $roleOptions[$name] = ucfirst($name);
        }

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

        return $this->render('update', [
            'model' => $model,
            'roleOptions' => $roleOptions,
        ]);
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

    public function actionMeuPerfil()
    {
        if (Yii::$app->user->isGuest) {
            throw new \yii\web\ForbiddenHttpException('Precisa de iniciar sessão.');
        }

        $userId = Yii::$app->user->id;
        $model = \common\models\UserProfile::findOne(['user_id' => $userId]);

        if (!$model) {
            Yii::$app->session->setFlash('info', 'Ainda não possui um perfil. Crie um agora.');
            return $this->redirect(['create']);
        }

        return $this->render('meu-perfil', ['model' => $model]);
    }

    protected function findModelByUser($userId)
    {
        if (($model = \common\models\UserProfile::findOne(['user_id' => $userId])) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('Perfil não encontrado.');
    }
}
