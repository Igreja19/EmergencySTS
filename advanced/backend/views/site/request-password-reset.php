<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Recuperar Palavra-passe';
?>
    <div class="card">
        <div class="card-body login-card-body">

            <div class="text-center mb-4">
                <span class="fas fa-key fa-3x text-muted"></span>
                <h4 class="mt-3 mb-1"><?= Html::encode($this->title) ?></h4>
                <p class="text-muted">Insira o seu email para receber o link de recuperação.</p>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'request-password-reset-form',
                'layout' => 'default',
            ]); ?>

            <?= $form->field($model, 'email')
                ->textInput(['placeholder' => 'utilizador@hospital.pt', 'autofocus' => true])
                ->label('Email Institucional')
            ?>

            <div class="row mt-4">
                <div class="col-12">
                    <?= Html::submitButton('Enviar Pedido &rarr;', ['class' => 'btn btn-success btn-block']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <p class="mb-1 text-center mt-3">
                <?= Html::a('Voltar ao Login', ['site/login']) ?>
            </p>
        </div>
    </div>
<?php
