<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\PrescricaoSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="prescricao-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'medicamento') ?>

    <?= $form->field($model, 'dosagem') ?>

    <?= $form->field($model, 'frequencia') ?>

    <?= $form->field($model, 'observacoes') ?>

    <?php // echo $form->field($model, 'dataprescricao') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
