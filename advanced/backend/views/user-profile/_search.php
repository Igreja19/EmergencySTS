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

        <div class="row g-3 align-items-end justify-content-center">
            <div class="col-md-6">
                <?= $form->field($model, 'q')->textInput([
                        'placeholder' => '🔍 Pesquisar por nome, email, NIF ou telefone...',
                        'class' => 'form-control shadow-sm border border-success rounded-pill px-3'
                ])->label(false) ?>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <div class="d-flex justify-content-end gap-2">
                        <?= Html::submitButton('<i class="bi bi-search"></i> Pesquisar', [
                                'class' => 'btn btn-success px-4 fw-semibold'
                        ]) ?>
                        <?= Html::a('<i class="bi bi-x-circle"></i> Limpar', ['index'], [
                                'class' => 'btn btn-outline-secondary px-4 fw-semibold'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
