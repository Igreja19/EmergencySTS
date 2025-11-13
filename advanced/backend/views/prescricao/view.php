<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = "Prescrição #" . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prescrições', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile.css');

?>

<div class="prescricao-view d-flex justify-content-center">
    <div class="card shadow-sm border-0 rounded-4 p-4 w-100" style="max-width: 900px;">

        <!-- TÍTULO -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold text-success mb-1">
                    <i class="bi bi-file-medical me-2"></i> <?= Html::encode($this->title) ?>
                </h3>
                <div class="text-muted">
                    <i class="bi bi-calendar-event me-1"></i>
                    <?= Yii::$app->formatter->asDatetime($model->dataprescricao, "php:d/m/Y H:i") ?>
                </div>
            </div>

            <span class="badge bg-danger px-3 py-2">
                <i class="bi bi-capsule me-1"></i> Prescrição
            </span>
        </div>

        <hr class="text-success opacity-25">

        <!-- SECÇÃO: DADOS DA PRESCRIÇÃO -->
        <h5 class="fw-bold text-success mt-3 mb-3">
            <i class="bi bi-journal-medical me-2"></i> Detalhes da Prescrição
        </h5>

        <div class="row mb-4">
            <div class="col-md-4 fw-semibold">ID</div>
            <div class="col-md-8"><?= $model->id ?></div>

            <div class="col-md-4 fw-semibold mt-3">Consulta Associada</div>
            <div class="col-md-8 mt-3">
                <?= Html::a(
                        'Consulta #' . $model->consulta_id,
                        ['consulta/view', 'id' => $model->consulta_id],
                        ['class' => 'text-decoration-none text-primary fw-semibold']
                ) ?>
            </div>

            <div class="col-md-4 fw-semibold mt-3">Data</div>
            <div class="col-md-8 mt-3">
                <?= Yii::$app->formatter->asDatetime($model->dataprescricao, "php:d/m/Y H:i") ?>
            </div>
        </div>

        <!-- SECÇÃO: OBSERVAÇÕES -->
        <h5 class="fw-bold text-success mt-3 mb-3">
            <i class="bi bi-chat-left-dots me-2"></i> Observações
        </h5>

        <div class="mb-4 bg-light p-3 rounded-3">
            <?= nl2br(Html::encode($model->observacoes)) ?>
        </div>

        <!-- BOTÕES -->
        <div class="d-flex justify-content-end gap-3 mt-4">
            <?= Html::a('<i class="bi bi-pencil me-1"></i> Editar', ['update', 'id' => $model->id], [
                    'class' => 'btn btn-success px-4 fw-semibold'
            ]) ?>

            <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Voltar', ['index'], [
                    'class' => 'btn btn-outline-secondary px-4 fw-semibold'
            ]) ?>
        </div>
    </div>
</div>
