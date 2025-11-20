<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/triagem/_form.css');

?>

<div class="triagem-form">

    <?php $form = ActiveForm::begin(); ?>

    <h5><i class="bi bi-person-lines-fill me-2"></i> Dados do Paciente</h5>
    <div class="row g-3 mb-3">

        <?php if ($model->isNewRecord): ?>

            <!-- CREATE — escolher paciente -->
            <div class="col-md-6">
                <?= $form->field($model, 'userprofile_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map(
                                \common\models\UserProfile::find()->all(),
                                'id',
                                'nome'
                        ),
                        ['prompt' => 'Selecione o paciente', 'class' => 'form-select']
                )->label('<i class="bi bi-person me-2"></i> Paciente'); ?>
            </div>

            <!-- CREATE — escolher pulseira -->
            <div class="col-md-6">
                <?= $form->field($model, 'pulseira_id')->dropDownList(
                        [],
                        ['prompt' => 'Selecione primeiro o paciente', 'id' => 'dropdown-pulseiras']
                )->label('<i class="bi bi-upc-scan me-2"></i> Código da Pulseira') ?>
            </div>

        <?php else: ?>

            <!-- UPDATE — mostrar nome do paciente -->
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    <i class="bi bi-person me-2"></i> Paciente
                </label>
                <input type="text"
                       class="form-control fw-bold"
                       value="<?= $model->userprofile->nome ?? '—' ?>"
                       readonly>
            </div>

            <!-- UPDATE — mostrar código da pulseira -->
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    <i class="bi bi-upc-scan me-2"></i> Código da Pulseira
                </label>
                <input type="text"
                       class="form-control fw-bold"
                       value="<?= $model->pulseira->codigo ?? '—' ?>"
                       readonly>
            </div>

            <!-- Hidden inputs para manter IDs no POST -->
            <?= Html::hiddenInput('Triagem[userprofile_id]', $model->userprofile_id) ?>
            <?= Html::hiddenInput('Triagem[pulseira_id]', $model->pulseira_id) ?>

        <?php endif; ?>

    </div>

    <h5><i class="bi bi-flag me-2"></i> Classificação de Prioridade</h5>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <?= $form->field($model, 'prioridade_pulseira')->dropDownList([
                    'Vermelho' => 'Vermelho',
                    'Laranja'  => 'Laranja',
                    'Amarelo'  => 'Amarelo',
                    'Verde'    => 'Verde',
                    'Azul'     => 'Azul',
            ], ['class' => 'form-select'])
             ?>
        </div>
    </div>

    <h5><i class="bi bi-clipboard-heart me-2"></i> Informação Clínica</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <?= $form->field($model, 'motivoconsulta')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Motivo da consulta']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'queixaprincipal')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Queixa principal']) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'descricaosintomas')
                    ->textarea(['rows' => 3, 'placeholder' => 'Descrição dos sintomas']) ?>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <?= $form->field($model, 'iniciosintomas')
                    ->input('datetime-local', ['placeholder' => 'Data e hora do início dos sintomas']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'intensidadedor')
                    ->input('number', ['min' => 0, 'max' => 10, 'placeholder' => 'Intensidade da dor (0-10)']) ?>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <?= $form->field($model, 'alergias')
                    ->textarea(['rows' => 2, 'placeholder' => 'Alergias conhecidas']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'medicacao')
                    ->textarea(['rows' => 2, 'placeholder' => 'Medicação atual']) ?>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <?= $form->field($model, 'datatriagem')
                    ->input('datetime-local', ['placeholder' => 'Data e hora da triagem']) ?>
        </div>
    </div>

    <div class="form-group text-center mt-4">
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Guardar', ['class' => 'btn btn-save']) ?>
    </div>

    <?php
    $this->registerJsFile(Yii::$app->request->baseUrl . '/js/triagem/_form.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    ?>
    <?php ActiveForm::end(); ?>
</div>
