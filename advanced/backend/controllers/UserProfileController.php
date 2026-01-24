<?php

namespace backend\controllers;

use common\models\Notificacao;
use common\models\User;
use Yii;
use common\models\UserProfile;
use common\models\UserProfileSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
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

        $roles = Yii::$app->authManager->getRoles();
        $roleOptions = [];
        foreach ($roles as $name => $role) {
            $roleOptions[$name] = ucfirst($name);
        }

        if ($model->load(Yii::$app->request->post())) {

            // Criar utilizador base se não existir
            $user = User::findOne(['email' => $model->email]);
            if (!$user) {
                $user = new User();
                $user->username = $model->nome;
                $user->email = $model->email;
                $user->setPassword('admin123'); // senha padrão
                $user->generateAuthKey();
                $user->status = User::STATUS_ACTIVE;

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

                // Notificação envia para o ADMIN (não para o user criado)
                $adminProfileId = Yii::$app->user->identity->userprofile->id;

                Notificacao::enviar(
                    $adminProfileId,
                    "Novo utilizador criado",
                    "Foi criada uma nova conta: {$model->nome}",
                    "Geral"
                );

                Yii::$app->session->setFlash('success', 'Utilizador criado com sucesso!');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao guardar o perfil: ' . json_encode($model->getErrors()));
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

        $roles = Yii::$app->authManager->getRoles();
        $roleOptions = [];
        foreach ($roles as $name => $role) {
            $roleOptions[$name] = ucfirst($name);
        }

        // Evita mostrar hash da password
        $model->password = '';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // === Sempre buscar o USER associado ===
            $user = User::findOne($model->user_id);

            if ($user) {

                if ($oldEmail !== $model->email) {
                    $user->email = $model->email;
                }
                if (!empty($model->nome)) {
                    $user->username = $model->nome;
                }

                if (!empty($model->password)) {
                    $user->setPassword($model->password);
                    $user->generateAuthKey();
                }

                $user->save(false);
            }

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
        }

        $auth = Yii::$app->authManager;
        $userRoles = $auth->getRolesByUser($model->user_id);

        if (!empty($userRoles)) {
            // pega só a primeira role
            $model->role = array_keys($userRoles)[0];
        }

        return $this->render('update', [
            'model' => $model,
            'roleOptions' => $roleOptions,
        ]);
    }


    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // User associado (autenticação)
        $user = $model->user;

        // Remover roles/permissões
        if ($user) {
            Yii::$app->authManager->revokeAll($user->id);
        }

        //  Apagar UserProfile
        $model->delete();

        //  Apagar User
        if ($user) {
            $user->delete();
        }

        // Notificação (admin que executa a ação)
        $adminProfileId = Yii::$app->user->identity->userprofile->id;

        Notificacao::enviar(
            $adminProfileId,
            'Utilizador eliminado',
            "A conta {$model->nome} foi eliminada.",
            'Geral'
        );

        Yii::$app->session->setFlash(
            'success',
            'Utilizador apagado com sucesso.'
        );

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
            throw new ForbiddenHttpException('Precisa de iniciar sessão.');
        }

        $userId = Yii::$app->user->id;
        $model = UserProfile::findOne(['user_id' => $userId]);

        if (!$model) {
            Yii::$app->session->setFlash('info', 'Ainda não possui um perfil. Crie um agora.');
            return $this->redirect(['create']);
        }

        return $this->render('meu-perfil', ['model' => $model]);
    }

    protected function findModelByUser($userId)
    {
        if (($model = UserProfile::findOne(['user_id' => $userId])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Perfil não encontrado.');
    }
    public function actionAtivar($id)
    {
        $model = $this->findModel($id);

        // Já está ativo
        if ($model->isAtivo()) {
            Yii::$app->session->setFlash('info', 'Utilizador já está ativo.');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        $user = $model->user;

        if ($user) {
            // Permitir login
            $user->status = User::STATUS_ACTIVE;
            $user->save(false);
        }

        // Reativar perfil
        $model->ativar();

        // Notificação
        $adminProfileId = Yii::$app->user->identity->userprofile->id;

        Notificacao::enviar(
            $adminProfileId,
            'Utilizador ativado',
            "A conta {$model->nome} foi ativada.",
            'Geral'
        );

        Yii::$app->session->setFlash(
            'success',
            'Utilizador ativado com sucesso.'
        );

        return $this->redirect(['update', 'id' => $model->id]);
    }
    public function actionDesativar($id)
    {
        $model = $this->findModel($id);

        if (!$model->isAtivo()) {
            Yii::$app->session->setFlash('warning', 'Utilizador já está desativado.');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        $user = $model->user;

        if ($user) {
            $user->status = User::STATUS_INACTIVE;
            $user->save(false);

            Yii::$app->authManager->revokeAll($user->id);
        }

        $model->desativar();

        // Notificação
        $adminProfileId = Yii::$app->user->identity->userprofile->id;

        Notificacao::enviar(
            $adminProfileId,
            'Utilizador ativado',
            "A conta {$model->nome} foi ativada.",
            'Geral'
        );

        Yii::$app->session->setFlash(
            'success',
            'Utilizador desativado com sucesso.'
        );

        return $this->redirect(['update', 'id' => $model->id]);
    }
}
