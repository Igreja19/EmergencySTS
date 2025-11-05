<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */
/** @var array $roleOptions */

?>

<?php $form = ActiveForm::begin(); ?>

<h5 class="fw-bold text-success mb-3">
    <i class="bi bi-person-lines-fill me-2"></i> Dados do Utilizador
</h5>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'nome')->textInput(['maxlength' => true, 'placeholder' => 'Nome completo']) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'email')->input('email', ['placeholder' => 'Email']) ?>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'telefone')->textInput(['maxlength' => true, 'placeholder' => '9XXXXXXXX']) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'nif')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'sns')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <?= $form->field($model, 'genero')->dropDownList([
                'M' => 'Masculino',
                'F' => 'Feminino',
                'O' => 'Outro',
        ], ['prompt' => '— Selecionar —']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'datanascimento')->input('date') ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'morada')->textInput(['maxlength' => true, 'placeholder' => 'Morada']) ?>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'role')->dropDownList($roleOptions, [
                'prompt' => '— Selecionar função —'
        ])->label('Função / Role') ?>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <?= Html::submitButton('Guardar', ['class' => 'btn btn-save']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-cancel']) ?>
</div>

<?php ActiveForm::end(); ?>
