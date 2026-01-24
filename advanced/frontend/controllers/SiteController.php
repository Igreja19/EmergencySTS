<?php

namespace frontend\controllers;

use common\models\LoginForm;
use common\models\User;
use frontend\models\ContactForm;
use frontend\models\DoctorCard;
use frontend\models\DoctorCards;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup','index','acesso-restrito'],
                'rules' => [
                    [
                        'actions' => ['signup','acesso-restrito'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow'   => true,
                        'roles'   => ['paciente'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'  => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class'           => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $doutores = DoctorCards::getDoutoresFicticios();

        return $this->render('index', [
            'doutores' => $doutores,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        $model->scenario = LoginForm::SCENARIO_FRONTEND;

        $contaDesativada = false;

        if ($model->load(Yii::$app->request->post())) {

            $user = User::findOne(['username' => $model->username]);

            if ($user && $user->userprofile && !$user->userprofile->isAtivo()) {
                $contaDesativada = true;
            }
            elseif ($model->login()) {

                $user = Yii::$app->user->identity;

                // (Tua lógica existente: Primeiro Login)
                if ($user->primeiro_login) {
                    Yii::$app->session->set('firstLogin', true);
                    $user->primeiro_login = 0;
                    $user->save(false);
                }

                return $this->goBack();
            }
            else {
                if ($model->acessoRestrito) {
                    return $this->redirect(['/site/acesso-restrito']);
                }
                if (!$model->hasErrors()) {
                    $model->addError('password', 'Incorrect username or password.');
                }
            }
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
            'contaDesativada' => $contaDesativada,
        ]);
    }


    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Obrigado por entrar em contacto connosco. Entraremos em contacto consigo o mais breve possível.');
            } else {
                Yii::$app->session->setFlash('error', 'Houve um erro ao enviar a sua mensagem. Por favor, tente novamente mais tarde.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Registo de utilizador (signup).
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {

            // Criar utilizador
            $user = $model->signup();
            if ($user) {

                // ==============================
                // RBAC — ATRIBUIÇÃO DA ROLE PACIENTE
                // ==============================
                $auth = Yii::$app->authManager;

                // Obter role paciente (não criar várias vezes)
                $pacienteRole = $auth->getRole('paciente');
                if ($pacienteRole === null) {
                    $pacienteRole = $auth->createRole('paciente');
                    $pacienteRole->description = 'Paciente do sistema';
                    $auth->add($pacienteRole);
                }

                // Só atribuir se ainda não tiver
                if ($auth->getAssignment('paciente', $user->id) === null) {
                    $auth->assign($pacienteRole, $user->id);
                }

                // ==============================
                // REDIRECIONAR PARA LOGIN
                // ==============================
                Yii::$app->session->setFlash(
                    'success',
                    'Conta criada com sucesso! Faça login para continuar.'
                );

                return $this->redirect(['site/login']);
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }


    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address.
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return \yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email.
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model,
        ]);
    }
    public function actionAcessoRestrito()
    {
        $this->layout = 'main-login';

        $this->actionDestroyCookie();

        return $this->render('acesso-restrito');
    }
    private function actionDestroyCookie()
    {
        $cookies = Yii::$app->response->cookies;

        // frontend identity cookie
        $cookies->remove('advanced-frontend');

        Yii::$app->user->logout(false);
        Yii::$app->session->destroy();
    }
}
