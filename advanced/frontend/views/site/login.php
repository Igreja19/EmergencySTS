<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Iniciar Sessão';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/site/login.css');
?>
<section class="min-vh-100 d-flex align-items-center justify-content-center login-bg">
    <div class="login card shadow-sm border-0 w-100 mx-3">
        <div class="card-body p-5">

            <h3 class="text-center fw-bold mb-3 text-dark"><?= Html::encode($this->title) ?></h3>
            <p class="text-center text-muted mb-4">Aceda à sua área de paciente</p>

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                    <?= $form->field($model, 'username')->textInput([
                            'autofocus' => true,
                            'placeholder' => 'username',
                            'class' => 'form-control form-control-lg rounded-3 mb-3'
                    ])->label('Nome de utilizador') ?>

                    <?= $form->field($model, 'password')->passwordInput([
                            'placeholder' => '••••••••',
                            'class' => 'form-control form-control-lg rounded-3 mb-3'
                    ])->label('Palavra-passe') ?>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <?= $form->field($model, 'rememberMe')->checkbox(['label' => 'Lembrar-me']) ?>

                        <div class="text-end small">
                            <a href="<?= Yii::$app->urlManager->createUrl(['site/request-password-reset']) ?>" class="text-decoration-none text-primary fw-semibold">
                                Recuperar palavra-passe
                            </a>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <?= Html::submitButton('<i class="bi bi-box-arrow-in-right me-2"></i>Entrar', [
                                'class' => 'btn btn-dark btn-lg fw-semibold rounded-3',
                                'name' => 'login-button'
                        ]) ?>
                    </div>

                    <div class="text-center small">
                        <span class="text-muted">Não tem conta?</span>
                        <a href="<?= Yii::$app->urlManager->createUrl(['site/signup']) ?>" class="text-primary fw-semibold text-decoration-none">Criar conta</a>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
