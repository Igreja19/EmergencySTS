<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\TriagemSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="triagem-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'motivoconsulta') ?>

    <?= $form->field($model, 'queixaprincipal') ?>

    <?= $form->field($model, 'descricaosintomas') ?>

    <?= $form->field($model, 'iniciosintomas') ?>

    <?php // echo $form->field($model, 'intensidadedor') ?>

    <?php // echo $form->field($model, 'alergias') ?>

    <?php // echo $form->field($model, 'medicacao') ?>

    <?php // echo $form->field($model, 'motivo') ?>

    <?php // echo $form->field($model, 'datatriagem') ?>

    <?php // echo $form->field($model, 'userprofile_id') ?>

    <?php // echo $form->field($model, 'pulseira_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
