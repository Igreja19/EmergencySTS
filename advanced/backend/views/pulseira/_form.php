<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\UserProfile;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */
/** @var common\models\Triagem|null $triagem */

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/pulseira/_form.css');

$pacientes = UserProfile::find()
        ->select(['nome'])
        ->indexBy('id')
        ->column();
?>

<div class="pulseira-form">

    <?php $form = ActiveForm::begin(); ?>

    <h5><i class="bi bi-pencil-square me-2"></i>
        <?= $model->isNewRecord ? 'Criar Pulseira' : 'Editar Pulseira' ?>
    </h5>

    <div class="row g-3 mb-3">

        <!-- UPDATE (mostrar campos da pulseira) -->
        <?php if (!$model->isNewRecord): ?>

            <!-- CÓDIGO -->
            <div class="col-md-6">
                <?= $form->field($model, 'codigo')->textInput([
                        'readonly' => true,
                        'class' => 'form-control fw-bold'
                ])->label('Código da Pulseira') ?>
            </div>

            <!-- PACIENTE -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Paciente</label>
                <input type="text" class="form-control fw-bold"
                       value="<?= $model->userprofile->nome ?>" readonly>
            </div>

            <!-- PRIORIDADE -->
            <div class="col-md-6">
                <?= $form->field($model, 'prioridade')->dropDownList([
                        'Vermelho' => 'Vermelho',
                        'Laranja'  => 'Laranja',
                        'Amarelo'  => 'Amarelo',
                        'Verde'    => 'Verde',
                        'Azul'     => 'Azul',
                ])->label('Prioridade') ?>
            </div>

            <!-- ESTADO -->
            <div class="col-md-6">
                <?= $form->field($model, 'status')->dropDownList([
                        'Em espera'        => '⏳ A aguardar Atendimento',
                        'Em atendimento'   => '🩺 Em Atendimento',
                        'Atendido'         => '✅ Atendido',
                ], [
                        'disabled' => true,
                ])->label('Estado') ?>
            </div>

            <!-- TEMPO ENTRADA -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Tempo de Entrada</label>
                <input type="text" class="form-control fw-bold"
                       value="<?= Yii::$app->formatter->asDatetime($model->tempoentrada, 'php:d/m/Y H:i') ?>"
                       readonly>
            </div>

            <!-- TRIAGEM -->
            <?php $fromPulseira = Yii::$app->request->get('pulseira_id'); ?>
            <div class="col-md-6">
                <label class="form-label fw-bold">Triagem Associada</label>
                <div class="pt-2">
                    <?= $model->triagem
                            ? Html::a(
                                    'Ver Triagem #' . $model->triagem->id,
                                    ['triagem/view', 'id' => $model->triagem->id, 'pulseira_id' => $model->id],
                                    ['class' => 'text-success fw-semibold']
                            )
                            : '<span class="text-muted">—</span>'; ?>
                </div>
            </div>

        <?php endif; ?>

        <!-- CREATE (criar pulseira pendente) -->
        <?php if ($model->isNewRecord): ?>

            <!-- Paciente -->
            <div class="col-md-6">
                <?= $form->field($model, 'userprofile_id')
                        ->dropDownList($pacientes, [
                                'prompt' => 'Selecione o paciente',
                                'class' => 'form-select'
                        ])
                        ->label('<i class="bi bi-person me-2"></i> Paciente'); ?>
            </div>

            <!-- Prioridade Automática -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Prioridade</label>
                <input type="text" class="form-control" value="Pendente" readonly>
            </div>

            <!-- Estado Automático -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Estado</label>
                <input type="text" class="form-control" value="Em espera" readonly>
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
