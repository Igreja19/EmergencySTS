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

            <div class="col-md-3">
                <?= $form->field($model, 'created_at')->input('date', [
                        'class' => 'form-control shadow-sm border border-success rounded-pill px-3'
                ])->label(false) ?>
            </div>

            <div class="col-md-3 text-center text-md-start">
                <div class="d-flex justify-content-center justify-content-md-start gap-2">
                    <?= Html::submitButton('<i class="bi bi-search me-1"></i> Procurar', [
                            'class' => 'btn btn-success px-4 py-2 shadow-sm rounded-pill fw-semibold'
                    ]) ?>
                    <?= Html::a('<i class="bi bi-x-circle me-1"></i> Limpar', ['index'], [
                            'class' => 'btn btn-outline-secondary px-4 py-2 shadow-sm rounded-pill fw-semibold',
                            'title' => 'Limpar filtros'
                    ]) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
