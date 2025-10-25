<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Observacao $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="observacao-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'descricao')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sintomas')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'dataregisto')->textInput() ?>

    <?= $form->field($model, 'consulta_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
