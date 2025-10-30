<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\UserProfileSearch $model */
?>

<div class="card border-0 shadow-sm mb-3 rounded-4">
    <div class="card-body py-3">
        <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['data-pjax' => 1],
        ]); ?>

        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <?= $form->field($model, 'q')->textInput([
                        'placeholder' => '🔍 Pesquisar por nome, email, NIF ou telefone...',
                        'class' => 'form-control shadow-sm border border-success rounded-3 px-3'
                ])->label(false) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'genero')->dropDownList([
                        '' => 'Todos os géneros',
                        'M' => 'Masculino',
                        'F' => 'Feminino',
                        'O' => 'Outro',
                ], [
                        'class' => 'form-select shadow-sm border border-success rounded-3',
                ])->label(false) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'ativo')->dropDownList([
                        '' => 'Estado',
                        'ativo' => 'Ativo',
                        'inativo' => 'Inativo',
                ], [
                        'class' => 'form-select shadow-sm border border-success rounded-3',
                ])->label(false) ?>
            </div>

            <div class="col-md-2 text-end">
                <?= Html::submitButton('<i class="bi bi-search me-1"></i> Procurar', [
                        'class' => 'btn btn-success px-4 py-2 shadow-sm rounded-3'
                ]) ?>
                <?= Html::a('<i class="bi bi-x-circle me-1"></i>', ['index'], [
                        'class' => 'btn btn-outline-secondary px-3 py-2 shadow-sm rounded-3',
                        'title' => 'Limpar filtros'
                ]) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
