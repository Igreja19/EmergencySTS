<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\PulseiraSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="pulseira-search mb-3">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1,
                    'class' => 'row g-2 align-items-center justify-content-center',
            ],
    ]); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'codigo')->textInput([
                'placeholder' => 'Código da pulseira...',
                'class' => 'form-control rounded-pill shadow-sm border-success'
        ])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'prioridade')->dropDownList([
                '' => 'Todas as prioridades',
                'Vermelho' => 'Vermelho',
                'Laranja'  => 'Laranja',
                'Amarelo'  => 'Amarelo',
                'Verde'    => 'Verde',
                'Azul'     => 'Azul',
        ], ['class' => 'form-select rounded-pill shadow-sm border-success'])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'status')->dropDownList([
                '' => 'Todos os estados',
                'Aguardando' => 'Aguardando Atendimento',
                'Atendida'   => 'Atendida',
                'Encerrada'  => 'Encerrada',
        ], ['class' => 'form-select rounded-pill shadow-sm border-success'])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'tempoentrada')->input('date', [
                'class' => 'form-control rounded-pill shadow-sm border-success'
        ])->label(false) ?>
    </div>

    <div class="col-md-12 text-center mt-2">
        <?= Html::submitButton('<i class="bi bi-search"></i> Pesquisar', [
                'class' => 'btn btn-success rounded-pill px-4 fw-semibold shadow-sm'
        ]) ?>
        <?= Html::a('<i class="bi bi-x-circle"></i> Limpar', ['index'], [
                'class' => 'btn btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
