<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Criar Conta';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/site/signup.css');
?>

<section class="min-vh-100 d-flex align-items-center justify-content-center login-bg">
    <div class="signup card shadow-sm border-0 w-100 mx-3">
        <div class="card-body p-5">

            <h3 class="text-center fw-bold mb-3 text-dark"><?= Html::encode($this->title) ?></h3>
            <p class="text-center text-muted mb-4">Crie a sua conta de paciente</p>

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                    <?= $form->field($model, 'username')->textInput([
                            'autofocus' => true,
                            'placeholder' => 'Nome de utilizador',
                            'class' => 'form-control form-control-lg rounded-3 mb-3'
                    ])->label('Nome de utilizador') ?>

                    <?= $form->field($model, 'email')->textInput([
                            'placeholder' => 'exemplo@email.pt',
                            'class' => 'form-control form-control-lg rounded-3 mb-3'
                    ])->label('Email') ?>

                    <?= $form->field($model, 'password')->passwordInput([
                            'placeholder' => '••••••••',
                            'class' => 'form-control form-control-lg rounded-3 mb-4'
                    ])->label('Palavra-passe') ?>

                    <div class="d-grid mb-3">
                        <?= Html::submitButton('<i class="bi bi-person-plus-fill me-2"></i>Criar Conta', [
                                'class' => 'btn btn-success btn-lg fw-semibold rounded-3',
                                'name' => 'signup-button'
                        ]) ?>
                    </div>

                    <div class="text-center small">
                        <span class="text-muted">Já tem conta?</span>
                        <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="text-primary fw-semibold text-decoration-none">
                            Iniciar sessão
                        </a>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
