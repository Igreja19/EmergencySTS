<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile.css');

?>

<div class="userprofile-form">

    <h5 class="fw-bold text-success mb-3">
        <i class="bi bi-file-earmark-plus me-2"></i>
        <?= $model->isNewRecord ? 'Criar Prescrição' : 'Editar Prescrição' ?>
    </h5>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row g-3">
        <div class="col-md-12">
            <?= $form->field($model, 'observacoes')->textarea([
                    'rows' => 3,
                    'placeholder' => 'Detalhes da prescrição...'
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'consulta_id')->dropDownList(
                    ArrayHelper::map(
                            \common\models\Consulta::find()->all(),
                            'id',
                            fn($c) => "Consulta #" . $c->id
                    ),
                    ['prompt' => '— Selecionar Consulta —']
            ) ?>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <?= Html::submitButton('Guardar', ['class' => 'btn-save']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn-cancel']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">