<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="triagem-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nomecompleto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'datanascimento')->textInput() ?>

    <?= $form->field($model, 'sns')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'motivoconsulta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'queixaprincipal')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'descricaosintomas')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'iniciosintomas')->textInput() ?>

    <?= $form->field($model, 'intensidadedor')->textInput() ?>

    <?= $form->field($model, 'condicoes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'alergias')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'medicacao')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'prioridadeatribuida')->dropDownList([ 'Vermelho' => 'Vermelho', 'Laranja' => 'Laranja', 'Amarelo' => 'Amarelo', 'Verde' => 'Verde', 'Azul' => 'Azul', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'datatriagem')->textInput() ?>

    <?= $form->field($model, 'discriminacaoprincipal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'paciente_id')->textInput() ?>

    <?= $form->field($model, 'utilizador_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
