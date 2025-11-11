<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ConsultaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="consulta-search card border-0 p-3 shadow-sm rounded-4 mb-4">
    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
    ]); ?>

    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <?= $form->field($model, 'estado')->dropDownList([
                    '' => 'Selecionar',
                    'Aberta' => 'Aberta',
                    'Em curso' => 'Em curso',
                    'Encerrada' => 'Encerrada',
            ], ['class' => 'form-select']) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'data_consulta')->input('date') ?>
        </div>

        <div class="col-md-4 text-end">
            <?= Html::submitButton('<i class="bi bi-search"></i> Pesquisar', [
                    'class' => 'btn btn-success px-4 fw-semibold'
            ]) ?>
            <?= Html::a('<i class="bi bi-x-circle"></i> Limpar', ['index'], [
                    'class' => 'btn btn-outline-secondary px-4 fw-semibold'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
