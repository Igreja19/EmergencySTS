<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCss('
.pulseira-form {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  padding: 25px 30px;
  margin-bottom: 25px;
}
.pulseira-form h5 {
  color: #198754;
  font-weight: 700;
  margin-bottom: 15px;
}
.pulseira-form .form-control {
  border-radius: 12px;
  box-shadow: none;
  border: 1px solid #ced4da;
  padding: 10px 12px;
}
.pulseira-form .form-control:focus {
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

<div class="pulseira-form">

    <?php $form = ActiveForm::begin(); ?>

    <h5><i class="bi bi-upc me-2"></i> Dados da Pulseira</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <?= $form->field($model, 'codigo')->textInput(['maxlength' => true, 'placeholder' => 'Código da pulseira']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'prioridade')->dropDownList([
                    'Vermelho' => 'Vermelho',
                    'Laranja'  => 'Laranja',
                    'Amarelo'  => 'Amarelo',
                    'Verde'    => 'Verde',
                    'Azul'     => 'Azul',
            ], ['prompt' => 'Selecione a prioridade...', 'class' => 'form-control']) ?>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <?= $form->field($model, 'tempoentrada')->input('datetime-local') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->textInput(['maxlength' => true, 'placeholder' => 'Estado da pulseira (ex: Ativa, Encerrada)']) ?>
        </div>
    </div>

    <div class="form-group text-center mt-4">
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Guardar', ['class' => 'btn btn-save']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
