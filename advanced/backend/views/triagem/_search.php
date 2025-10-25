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

    <?= $form->field($model, 'nomecompleto') ?>

    <?= $form->field($model, 'datanascimento') ?>

    <?= $form->field($model, 'sns') ?>

    <?= $form->field($model, 'telefone') ?>

    <?php // echo $form->field($model, 'motivoconsulta') ?>

    <?php // echo $form->field($model, 'queixaprincipal') ?>

    <?php // echo $form->field($model, 'descricaosintomas') ?>

    <?php // echo $form->field($model, 'iniciosintomas') ?>

    <?php // echo $form->field($model, 'intensidadedor') ?>

    <?php // echo $form->field($model, 'condicoes') ?>

    <?php // echo $form->field($model, 'alergias') ?>

    <?php // echo $form->field($model, 'medicacao') ?>

    <?php // echo $form->field($model, 'motivo') ?>

    <?php // echo $form->field($model, 'prioridadeatribuida') ?>

    <?php // echo $form->field($model, 'datatriagem') ?>

    <?php // echo $form->field($model, 'discriminacaoprincipal') ?>

    <?php // echo $form->field($model, 'paciente_id') ?>

    <?php // echo $form->field($model, 'utilizador_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
