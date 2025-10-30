<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCss('
.triagem-form {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  padding: 25px 30px;
  margin-bottom: 25px;
}
.triagem-form h5 {
  color: #198754;
  font-weight: 700;
  margin-bottom: 15px;
}
.triagem-form .form-control {
  border-radius: 12px;
  box-shadow: none;
  border: 1px solid #ced4da;
  padding: 10px 12px;
}
.triagem-form .form-control:focus {
  border-color: #198754;
  box-shadow: 0 0 0 0.15rem rgba(25,135,84,.25);
}
.btn-save {
  background: linear-gradient(90deg, #198754 0%, #28a745 100%);
  color: #fff;
  font-weight: 600;
  border-radius: 12px;
  padding: 10px 25px;
  transition: .2s;
}
.btn-save:hover {
  opacity: .9;
  transform: translateY(-2px);
}
');
?>

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
