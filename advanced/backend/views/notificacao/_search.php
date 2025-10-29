<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\NotificacaoSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="notificacao-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'titulo') ?>

    <?= $form->field($model, 'mensagem') ?>

    <?= $form->field($model, 'tipo') ?>

    <?= $form->field($model, 'dataenvio') ?>

    <?php // echo $form->field($model, 'lida') ?>

    <?php // echo $form->field($model, 'userprofile_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
