<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var common\models\PacienteSearch $model */
?>
<div class="card bg-light mb-0">
    <div class="card-body py-2">
        <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['data-pjax' => 1],
        ]); ?>

        <div class="form-row">
            <div class="col-md-3 mb-2">
                <?= $form->field($model, 'q')->textInput(['placeholder'=>'Nome, NIF, Email, Telefone'])->label(false) ?>
            </div>
            <div class="col-md-3 mb-2">
                <?= $form->field($model, 'genero')->dropDownList([
                        '' => 'Género',
                        'Masculino' => 'Masculino',
                        'Feminino' => 'Feminino',
                        'Outro' => 'Outro',
                ], ['class'=>'form-control'])->label(false) ?>
            </div>
            <div class="col-md-3 mb-2">
                <?= $form->field($model, 'datanascimento')->input('date')->label(false) ?>
            </div>
            <div class="col-md-3 mb-2 d-flex">
                <?= Html::submitButton('<i class="fas fa-search mr-1"></i> Procurar', ['class' => 'btn btn-outline-secondary mr-2']) ?>
                <?= Html::a('<i class="fas fa-times mr-1"></i> Limpar', ['index'], ['class' => 'btn btn-light']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
