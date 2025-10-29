<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Consulta $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="consulta-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'data_consulta')->textInput() ?>

    <?= $form->field($model, 'estado')->dropDownList([ 'Aberta' => 'Aberta', 'Encerrada' => 'Encerrada', 'Em curso' => 'Em curso', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'prioridade')->dropDownList([ 'Vermelho' => 'Vermelho', 'Laranja' => 'Laranja', 'Amarelo' => 'Amarelo', 'Verde' => 'Verde', 'Azul' => 'Azul', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'motivo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'observacoes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'userprofile_id')->textInput() ?>

    <?= $form->field($model, 'triagem_id')->textInput() ?>

    <?= $form->field($model, 'prescricao_id')->textInput() ?>

    <?= $form->field($model, 'data_encerramento')->textInput() ?>

    <?= $form->field($model, 'tempo_consulta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'relatorio_pdf')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
