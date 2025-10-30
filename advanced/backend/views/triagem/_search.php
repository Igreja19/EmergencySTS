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
            'options' => ['data-pjax' => 1, 'class' => 'row g-2 align-items-center justify-content-center']
    ]); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'motivoconsulta')->textInput([
                'placeholder' => 'Pesquisar por motivo da consulta...',
                'class' => 'form-control rounded-pill shadow-sm border-success'
        ])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'datatriagem')->input('date', [
                'class' => 'form-control rounded-pill shadow-sm border-success'
        ])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= Html::submitButton('<i class="bi bi-search"></i> Pesquisar', ['class' => 'btn btn-success rounded-pill px-4 fw-semibold shadow-sm']) ?>
        <?= Html::a('<i class="bi bi-x-circle"></i> Limpar', ['index'], ['class' => 'btn btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
