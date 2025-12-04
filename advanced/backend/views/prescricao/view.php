<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */

?>

<div class="prescricao-view">


    <div class="card shadow-sm" style="border-radius: 12px; max-width: 900px; margin: auto;">

        <!-- HEADER VERDE -->
        <div class="d-flex justify-content-between align-items-center p-3"
             style="background: #1f9d55; color: white; border-radius: 12px 12px 0 0;">
            <h4 class="m-0">
                <i class="bi bi-file-earmark-medical-fill me-2"></i>
                Prescrição #<?= $model->id ?>
            </h4>

            <div>
                <?= Html::a('<i class="bi bi-pencil-square"></i> Editar',
                        ['update', 'id' => $model->id],
                        ['class' => 'btn btn-light fw-bold me-2']
                ) ?>

                <?= Html::a('<i class="bi bi-arrow-left-circle"></i> Voltar',
                        ['index'],
                        ['class' => 'btn btn-outline-light fw-bold']
                ) ?>
            </div>
        </div>

        <div class="card-body p-4">

            <!-- DADOS DA PRESCRIÇÃO -->
            <h5 class="section-title mb-3">
                <i class="bi bi-info-circle-fill me-2"></i> Dados da Prescrição
            </h5>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Data da Prescrição:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($model->dataprescricao)) ?>
                </div>

                <div class="col-md-6">
                    <strong>Consulta:</strong><br>
                    <?= Html::a(
                            'Consulta #' . $model->consulta_id,
                            ['/consulta/view', 'id' => $model->consulta_id],
                            ['class' => 'text-success fw-bold']
                    ) ?>
                </div>
            </div>

            <hr>

            <!-- OBSERVAÇÕES -->
            <h5 class="section-title mb-3">
                <i class="bi bi-journal-text me-2"></i> Observações
            </h5>

            <div class="mb-4">
                <div class="form-control bg-light shadow-sm">
                    <?= nl2br(Html::encode($model->observacoes)) ?>
                </div>
            </div>

            <hr>

            <!-- MEDICAMENTOS -->
            <h5 class="section-title mb-3">
                <i class="bi bi-capsule me-2"></i> Medicamentos
            </h5>

            <?php if (empty($prescricaoMedicamentos)): ?>
                <p class="text-muted">Nenhum medicamento associado.</p>

            <?php else: ?>

                <div class="list-group">

                    <?php foreach ($prescricaoMedicamentos as $pm): ?>
                        <div class="list-group-item mb-2 shadow-sm" style="border-radius: 8px;">

                            <!-- Nome + dosagem -->
                            <div class="fw-bold text-success mb-1">
                                <?= Html::encode($pm->medicamento->nome) ?>
                                <?php if (!empty($pm->medicamento->dosagem)): ?>
                                    (<?= Html::encode($pm->medicamento->dosagem) ?>)
                                <?php endif; ?>
                            </div>

                            <!-- Posologia -->
                            <div class="text-muted">
                                <strong>Posologia:</strong>
                                <?= Html::encode($pm->posologia) ?>
                            </div>

                        </div>
                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <!-- BOTÃO PDF -->
            <?php if ($model->consulta->estado === 'Encerrada'): ?>
                <?= Html::a(
                        '<i class="bi bi-filetype-pdf"></i> PDF',
                        ['pdf', 'id' => $model->id],
                        ['class' => 'btn btn-danger fw-bold me-2', 'style' => 'color:white;']
                ) ?>
            <?php else: ?>
                <button class="btn btn-secondary fw-bold me-2" disabled>
                    <i class="bi bi-filetype-pdf"></i> PDF (disponível após encerrar)
                </button>
            <?php endif; ?>

        </div>
    </div>
</div>

