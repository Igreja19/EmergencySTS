<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="triagem-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'motivoconsulta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'queixaprincipal')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'descricaosintomas')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'iniciosintomas')->textInput() ?>

    <?= $form->field($model, 'intensidadedor')->textInput() ?>

    <?= $form->field($model, 'alergias')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'medicacao')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'motivo')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'datatriagem')->textInput() ?>

    <?= $form->field($model, 'userprofile_id')->textInput() ?>

    <?= $form->field($model, 'pulseira_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
