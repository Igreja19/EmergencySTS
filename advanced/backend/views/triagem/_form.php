<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */
/** @var yii\widgets\ActiveForm $form */

?>
<link rel="stylesheet" href="css/triagem.css">

<div class="triagem-form">

    <?php $form = ActiveForm::begin(); ?>

    <h5><i class="bi bi-person-lines-fill me-2"></i> Dados do Paciente</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <?= $form->field($model, 'userprofile_id')
                    ->textInput(['type' => 'number', 'placeholder' => 'ID do Paciente (userprofile_id)']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'pulseira_id')
                    ->textInput(['type' => 'number', 'placeholder' => 'ID da Pulseira (pulseira_id)']) ?>
        </div>
    </div>

    <h5><i class="bi bi-clipboard-heart me-2"></i> Informação Clínica</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <?= $form->field($model, 'motivoconsulta')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Motivo da consulta']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'queixaprincipal')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Queixa principal']) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'descricaosintomas')
                    ->textarea(['rows' => 3, 'placeholder' => 'Descrição dos sintomas']) ?>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <?= $form->field($model, 'iniciosintomas')
                    ->input('datetime-local', ['placeholder' => 'Data e hora do início dos sintomas']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'intensidadedor')
                    ->input('number', ['min' => 0, 'max' => 10, 'placeholder' => 'Intensidade da dor (0-10)']) ?>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <?= $form->field($model, 'alergias')
                    ->textarea(['rows' => 2, 'placeholder' => 'Alergias conhecidas']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'medicacao')
                    ->textarea(['rows' => 2, 'placeholder' => 'Medicação atual']) ?>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <?= $form->field($model, 'datatriagem')
                    ->input('datetime-local', ['placeholder' => 'Data e hora da triagem']) ?>
        </div>
    </div>

    <div class="form-group text-center mt-4">
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Guardar', ['class' => 'btn btn-save']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
