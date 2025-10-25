<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var \common\models\PacienteSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="paciente-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nomecompleto') ?>

    <?= $form->field($model, 'nif') ?>

    <?= $form->field($model, 'datanascimento') ?>

    <?= $form->field($model, 'genero') ?>

    <?php  echo $form->field($model, 'telefone') ?>

    <?php  echo $form->field($model, 'morada') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
