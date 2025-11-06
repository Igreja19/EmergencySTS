<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */
/** @var array|null $consultas */
?>

<div class="prescricao-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'observacoes')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dataprescricao')->input('datetime-local') ?>

    <?= $form->field($model, 'consulta_id')->dropDownList(
            $consultas ?? [],
            ['prompt' => 'Selecione a consulta']
    ) ?>

    <div class="mt-3">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
