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

    <?= $form->field($model, 'prioridade') ?>

    <?= $form->field($model, 'motivo') ?>

    <?php // echo $form->field($model, 'observacoes') ?>

    <?php // echo $form->field($model, 'userprofile_id') ?>

    <?php // echo $form->field($model, 'triagem_id') ?>

    <?php // echo $form->field($model, 'prescricao_id') ?>

    <?php // echo $form->field($model, 'data_encerramento') ?>

    <?php // echo $form->field($model, 'tempo_consulta') ?>

    <?php // echo $form->field($model, 'relatorio_pdf') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
