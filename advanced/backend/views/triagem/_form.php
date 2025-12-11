<?php

use common\models\UserProfile;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
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

            <?php
            // Buscar apenas pacientes que têm pulseira pendente
            $pacientesComPulseiraPendente = UserProfile::find()
                    ->joinWith('pulseiras')
                    ->where(['pulseira.prioridade' => 'Pendente'])
                    ->all();
            ?>

            <!-- CREATE — escolher paciente -->
            <div class="col-md-6">
                <?= $form->field($model, 'userprofile_id')->dropDownList(
                        ArrayHelper::map(
                                $pacientesComPulseiraPendente,
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
                        [
                                'prompt' => 'Selecione primeiro o paciente',
                                'style' => 'height: auto;'
                        ]
                )->label('<i class="bi bi-upc-scan me-2"></i> Código da Pulseira') ?>
            </div>

        <?php else: ?>

            <!-- UPDATE — nome do paciente -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Paciente</label>
                <input type="text" class="form-control fw-bold"
                       value="<?= $model->userprofile->nome ?>" readonly>
            </div>

            <!-- UPDATE — código da pulseira -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Código da Pulseira</label>
                <input type="text" class="form-control fw-bold"
                       value="<?= $model->pulseira->codigo ?>" readonly>
            </div>

            <!-- Hidden para manter os IDs -->
            <?= $form->field($model, 'userprofile_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'pulseira_id')->hiddenInput()->label(false) ?>

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
            ], ['prompt' => 'Selecione a Prioridade', 'class' => 'form-select'])
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
                    ->input('datetime-local', [
                            'placeholder' => 'Data e hora do início dos sintomas',
                            'onkeydown' => 'return false',
                            'onpaste' => 'return false',
                            'onclick' => 'this.showPicker()', // força a abrir o calendario
                    ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'intensidadedor')
                    ->dropDownList([
                            0 => '0 - Sem Dor',
                            1 => '1 - Muito Leve',
                            2 => '2 - Leve',
                            3 => '3 - Moderada',
                            4 => '4 - Moderada a Forte',
                            5 => '5 - Forte',
                            6 => '6 - Bastante Forte',
                            7 => '7 - Muito Forte',
                            8 => '8 - Intensa',
                            9 => '9 - Muito Intensa',
                            10 => '10 - Insuportável'
                    ], [
                            'prompt' => 'Selecione a intensidade da dor',
                            'class' => 'form-select rounded-3 shadow-sm'
                    ])
                    ->label('<i class="bi bi-emoji-expressionless me-2"></i> Intensidade da Dor (0-10)') ?>
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
                    ->input('datetime-local', [
                            'placeholder' => 'Data e hora do início dos sintomas',
                            'onkeydown' => 'return false',
                            'onpaste' => 'return false',
                            'onclick' => 'this.showPicker()', // força a abrir o calendario
                    ]) ?>
        </div>
    </div>

    <div class="form-group text-center mt-4">
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Guardar', ['class' => 'btn btn-save']) ?>
    </div>

    <?php
    // Gera a URL correta para o AJAX
    $ajaxUrl = Url::to(['triagem/pulseiras-por-paciente']);
    // Define a variável JS GLOBAL
    $this->registerJs(
            "window.triagemPulseirasUrl = " . json_encode($ajaxUrl) . ";",
            View::POS_HEAD
    );
    $this->registerJsFile(Yii::$app->request->baseUrl . '/js/triagem/_form.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    ?>
    <?php ActiveForm::end(); ?>
</div>
