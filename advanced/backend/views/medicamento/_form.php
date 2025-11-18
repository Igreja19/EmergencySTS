<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Medicamento $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/medicamento/_form.css');


?>

<div class="medicamento-form-card">

    <?php $form = ActiveForm::begin(); ?>

    <!-- DADOS PRINCIPAIS -->
    <div class="mb-3">
        <div class="section-title">
            <i class="bi bi-capsule-pill"></i>
            Dados do Medicamento
        </div>

        <div class="row g-3">
            <div class="col-md-7">
                <?= $form->field($model, 'nome')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ex.: Paracetamol'
                ]) ?>
            </div>

            <div class="col-md-5">
                <?= $form->field($model, 'dosagem')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ex.: 500mg, 1g, 20mg...'
                ]) ?>
            </div>
        </div>
    </div>

    <!-- INDICAÇÃO -->
    <div class="mb-3">
        <div class="section-title">
            <i class="bi bi-info-circle"></i>
            Indicação / Uso
        </div>

        <?= $form->field($model, 'indicacao')->textarea([
                'rows' => 2,
                'placeholder' => 'Descreve brevemente para que é utilizado este medicamento (ex.: dor e febre, infeções respiratórias, hipertensão, etc.)'
        ]) ?>
    </div>

    <!-- POSOLOGIA -->
    <div class="mb-3">
        <div class="section-title">
            <i class="bi bi-clipboard2-check"></i>
            Posologia Recomendada
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <?= $form->field($model, 'quantidade_diaria')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ex.: 1 comprimido 2x ao dia'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'duracao_tratamento')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ex.: 7 dias, 10 dias, uso contínuo'
                ]) ?>
            </div>
        </div>
    </div>

    <!-- BOTÕES -->
    <div class="mt-3 d-flex justify-content-end gap-2">
        <?= Html::submitButton(
                '<i class="bi bi-save2 me-1"></i> Guardar',
                ['class' => 'btn btn-save']
        ) ?>

        <?= Html::a(
                '<i class="bi bi-x-circle me-1"></i> Cancelar',
                ['index'],
                ['class' => 'btn btn-outline-secondary btn-cancel']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
