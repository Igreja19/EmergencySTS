<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Notificacao $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="notificacao-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'mensagem')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'tipo')->dropDownList([ 'Consulta' => 'Consulta', 'Prioridade' => 'Prioridade', 'Geral' => 'Geral', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'dataenvio')->textInput() ?>

    <?= $form->field($model, 'lida')->textInput() ?>

    <?= $form->field($model, 'paciente_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
