<?php
use yii\helpers\Html;
// Certifica-te que estás a usar o ActiveForm do Bootstrap 4
use yii\bootstrap4\ActiveForm;
?>
<div class="card">
    <div class="card-body login-card-body">

        <div class="text-center mb-4">
            <span class="fas fa-shield-alt fa-3x text-muted"></span>
            <h4 class="mt-3 mb-1">Back-Office - Acesso Restrito</h4>
            <p class="text-muted">Área exclusiva para funcionários do hospital</p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            // Removemos as classes de form-horizontal para um layout standard
            'layout' => 'default',
        ]); ?>


        <?= $form->field($model, 'username')
            ->textInput(['placeholder' => 'USERNAME'])
            ->label('Username') // O label que aparece na imagem
        ?>

        <?= $form->field($model, 'password')
            ->passwordInput(['placeholder' => '********'])
            ->label('Palavra-passe') // O label que aparece na imagem
        ?>

        <div class="row mt-4">
            <div class="col-12">
                <?= Html::submitButton('Iniciar Sessão &rarr;', ['class' => 'btn btn-success btn-block']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="alert alert-warning mt-4" role="alert">
            <p class="mb-1">
                <span class="fas fa-info-circle mr-2"></span>
                <strong>Acesso Seguro</strong>
            </p>
            <p class="mb-0" style="font-size: 0.9em;">
                Esta área é protegida. Apenas funcionários autorizados podem aceder.
            </p>
        </div>

        <p class="mb-1 text-center mt-3">
            <?= Html::a('Esqueceu-se da palavra-passe?', ['site/request-password-reset']) ?>
        </p>

    </div>
</div>