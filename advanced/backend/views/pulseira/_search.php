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
                    'class' => 'row g-2 align-items-center',
            ],
    ]); ?>

    <!-- Código -->
    <div class="col-md-3">
        <?= $form->field($model, 'codigo')->textInput([
                'placeholder' => 'Código da pulseira...',
                'class' => 'form-control rounded-pill shadow-sm border-success'
        ])->label(false) ?>
    </div>

    <!-- Prioridade -->
    <div class="col-md-3">
        <?= $form->field($model, 'prioridade')->dropDownList([
                '' => 'Todas as prioridades',
                'Vermelho' => 'Vermelho',
                'Laranja'  => 'Laranja',
                'Amarelo'  => 'Amarelo',
                'Verde'    => 'Verde',
                'Azul'     => 'Azul',
        ], [
                'class' => 'form-select rounded-pill shadow-sm border-success'
        ])->label(false) ?>
    </div>

    <!-- Estado -->
    <div class="col-md-3">
        <?= $form->field($model, 'status')->dropDownList([
                '' => 'Todos os estados',
                'Em espera' => 'A aguardar Atendimento',
                'Atendida'   => 'Atendida',
                'Encerrada'  => 'Encerrada',
        ], [
                'class' => 'form-select rounded-pill shadow-sm border-success'
        ])->label(false) ?>
    </div>

    <!-- Data -->
    <div class="col-md-2">
        <?= $form->field($model, 'tempoentrada')->input('date', [
                'class' => 'form-control rounded-pill shadow-sm border-success'
        ])->label(false) ?>
    </div>

    <!-- Botões: ficam na mesma linha -->
    <div class="col-md-1 d-flex gap-2">
        <?= Html::submitButton('<i class="bi bi-search"></i>', [
                'class' => 'btn btn-success rounded-pill px-3 fw-semibold shadow-sm w-100',
                'title' => 'Pesquisar'
        ]) ?>

        <?= Html::a('<i class="bi bi-x-circle"></i>', ['index'], [
                'class' => 'btn btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm w-100',
                'title' => 'Limpar'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
