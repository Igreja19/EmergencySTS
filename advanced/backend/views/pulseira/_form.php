<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */
/** @var array $triagensDropdown */
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/pulseira/_form.css');
?>

<div class="pulseira-form">

    <?php $form = ActiveForm::begin(); ?>

    <h5><i class="bi bi-upc me-2"></i> Criar Pulseira</h5>

    <div class="row g-3 mb-3">

        <?php if ($model->isNewRecord): ?>

            <!-- SELEÇÃO DE TRIAGEM -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Triagem</label>
                <?= Html::dropDownList(
                        'triagem_id',
                        null,
                        $triagensDropdown,
                        [
                                'class' => 'form-select',
                                'prompt' => '— Selecionar Triagem —'
                        ]
                ) ?>
            </div>

            <!-- PRIORIDADE AUTOMÁTICA -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Prioridade</label>
                <input type="text" class="form-control" value="Pendente" readonly>
            </div>

            <!-- ESTADO AUTOMÁTICO -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Estado</label>
                <input type="text" class="form-control" value="Em espera" readonly>
            </div>

            <!-- TEMPO DE ENTRADA AUTOMÁTICO -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Tempo de Entrada</label>
                <input type="text" class="form-control" value="<?= date('d/m/Y H:i') ?>" readonly>
            </div>

            <!-- CAMPOS ESCONDIDOS AUTOMÁTICOS -->
            <?= Html::hiddenInput('auto_generate', '1') ?>

        <?php else: ?>

            <!-- CÓDIGO NO UPDATE -->
            <div class="col-md-6">
                <?= $form->field($model, 'codigo')->textInput([
                        'readonly' => true,
                        'class' => 'form-control-plaintext fw-bold'
                ]) ?>
            </div>

            <!-- UTENTE -->
            <div class="col-md-6">
                <?= $form->field($model, 'userprofile_id')->textInput([
                        'readonly' => true,
                        'value' => $model->userprofile->nome,
                        'class' => 'form-control-plaintext fw-bold'
                ])->label('Utente') ?>
            </div>

            <!-- PRIORIDADE EDITÁVEL -->
            <div class="col-md-6">
                <?= $form->field($model, 'prioridade')->dropDownList([
                        'Vermelho' => 'Vermelho',
                        'Laranja'  => 'Laranja',
                        'Amarelo'  => 'Amarelo',
                        'Verde'    => 'Verde',
                        'Azul'     => 'Azul',
                ]) ?>
            </div>

            <!-- ESTADO -->
            <div class="col-md-6">
                <?= $form->field($model, 'status')->dropDownList([
                        'Em espera'        => 'Em espera',
                        'Em atendimento'   => 'Em atendimento',
                        'Atendido'         => 'Atendido',
                ]) ?>
            </div>

        <?php endif; ?>

    </div>

    <div class="text-center mt-4">
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Guardar', [
                'class' => 'btn btn-success px-4 py-2'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
