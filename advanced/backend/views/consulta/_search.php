<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ConsultaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="consulta-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'data_consulta') ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'diagnostico_id') ?>

    <?= $form->field($model, 'paciente_id') ?>

    <?php // echo $form->field($model, 'utilizador_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
