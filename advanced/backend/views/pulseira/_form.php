<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="pulseira-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'prioridade')->dropDownList([ 'Vermelho' => 'Vermelho', 'Laranja' => 'Laranja', 'Amarelo' => 'Amarelo', 'Verde' => 'Verde', 'Azul' => 'Azul', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'Aguardando' => 'Aguardando', 'Em atendimento' => 'Em atendimento', 'Atendido' => 'Atendido', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'tempoentrada')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
