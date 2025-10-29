<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="prescricao-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'medicamento')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dosagem')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'frequencia')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'observacoes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'dataprescricao')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
