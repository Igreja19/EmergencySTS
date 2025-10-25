<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\PulseiraSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="pulseira-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'codigo') ?>

    <?= $form->field($model, 'prioridade') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'tempoentrada') ?>

    <?php // echo $form->field($model, 'triagem_id') ?>

    <?php // echo $form->field($model, 'paciente_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
