<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

$roles = Yii::$app->authManager->getRoles();
$roleOptions = [];
foreach ($roles as $name => $role) {
    $roleOptions[$name] = ucfirst($name);
}
// Carrega o CSS global (com o mesmo estilo do _form da Triagem)
?>
<link rel="stylesheet" href="css/user-profile.css">


<div class="form-card"> <!-- mesmo estilo da Triagem -->

    <?php $form = ActiveForm::begin(['options' => ['class' => 'userprofile-form']]); ?>

    <!-- 🧍 Dados Pessoais -->
    <h5><i class="bi bi-person-badge me-2"></i> Dados Pessoais</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <?= $form->field($model, 'nome')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Nome completo'])
                    ->label('Nome') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')
                    ->input('email', ['placeholder' => 'Endereço de email'])
                    ->label('Email') ?>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <?= $form->field($model, 'telefone')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Contacto telefónico'])
                    ->label('Telefone') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'nif')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Número de Identificação Fiscal (NIF)'])
                    ->label('NIF') ?>
        </div>
    </div>

    <!-- ⚧️ Informação Adicional -->
    <h5><i class="bi bi-info-circle me-2"></i> Informação Adicional</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <?= $form->field($model, 'genero')
                    ->dropDownList([
                            '' => 'Selecione o género...',
                            'M' => 'Masculino',
                            'F' => 'Feminino',
                            'O' => 'Outro',
                    ], ['class' => 'form-select rounded-3'])
                    ->label('Género') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'datanascimento')
                    ->input('date')
                    ->label('Data de Nascimento') ?>
        </div>
    </div>

    <!-- 🏠 Morada -->
    <h5><i class="bi bi-house-door me-2"></i> Morada</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-12">
            <?= $form->field($model, 'morada')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Endereço completo'])
                    ->label('Morada') ?>
        </div>
    </div>
    <!-- 🧩 Função / Role -->
    <h5><i class="bi bi-person-gear me-2"></i> Função</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <?= $form->field($model, 'role')
                    ->dropDownList($roleOptions, [
                            'prompt' => 'Selecione a função...',
                            'class' => 'form-select rounded-3'
                    ])
                    ->label('Função (Role)') ?>
        </div>
    </div>
    <!-- 💾 Botões -->
    <div class="form-group text-center mt-4">
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Guardar', ['class' => 'btn btn-save']) ?>
        <?= Html::a('<i class="bi bi-x-circle me-1"></i> Cancelar', ['index'], ['class' => 'btn btn-cancel ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
