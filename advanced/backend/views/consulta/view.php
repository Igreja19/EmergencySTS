<?php

use yii\helpers\Html;

$this->title = 'Consulta #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Consultas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
.consulta-box {
    background: #fff;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    max-width: 950px;
    margin: 0 auto;
}
.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #198754;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.label-col {
    width: 240px;
    color: #198754;
    font-weight: 600;
}
.value-col {
    color: #333;
}
.badge-estado {
    padding: 6px 12px;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
}
.badge-Em\\ curso { background: #0d6efd; }
.badge-Encerrada { background: #dc3545; }
");

?>

<div class="consulta-box">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-success d-flex align-items-center">
            <i class="bi bi-journal-medical me-2"></i> <?= Html::encode($this->title) ?>
        </h3>

        <div>
            <?= Html::a('<i class="bi bi-pencil-square me-1"></i> Editar',
                    ['update', 'id' => $model->id],
                    ['class' => 'btn btn-success rounded-4 px-4']) ?>

            <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Voltar',
                    ['index'],
                    ['class' => 'btn btn-outline-success rounded-4 px-4 ms-2']) ?>
        </div>
    </div>

    <hr>

    <!-- ======================== -->
    <!-- DADOS DA CONSULTA -->
    <!-- ======================== -->
    <div class="mb-4">
        <div class="section-title">
            <i class="bi bi-clipboard2-pulse"></i> Dados da Consulta
        </div>

        <div class="row mb-2">
            <div class="col label-col">Data da Consulta</div>
            <div class="col value-col">
                <?= Yii::$app->formatter->asDatetime($model->data_consulta, 'php:d/m/Y H:i') ?>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col label-col">Estado</div>
            <div class="col value-col">
                <span class="badge-estado badge-<?= $model->estado ?>">
                    <?= $model->estado ?>
                </span>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col label-col">Data Encerramento</div>
            <div class="col value-col">
                <?= $model->data_encerramento
                        ? Yii::$app->formatter->asDatetime($model->data_encerramento, 'php:d/m/Y H:i')
                        : '-' ?>
            </div>
        </div>
    </div>

    <!-- ======================== -->
    <!-- DADOS DO PACIENTE -->
    <!-- ======================== -->
    <div class="mb-4">
        <div class="section-title">
            <i class="bi bi-person-lines-fill"></i> Dados do Paciente
        </div>

        <div class="row mb-2">
            <div class="col label-col">Nome</div>
            <div class="col value-col"><?= $model->userprofile->nome ?></div>
        </div>

        <div class="row mb-2">
            <div class="col label-col">Triagem</div>
            <div class="col value-col">
                <?= Html::a(
                        'Ver Triagem #' . $model->triagem->id,
                        ['triagem/view', 'id' => $model->triagem->id],
                        ['class' => 'text-success fw-bold']
                ) ?>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col label-col">Pulseira</div>
            <div class="col value-col">
                <?= $model->triagem->pulseira
                        ? Html::a(
                                $model->triagem->pulseira->codigo,
                                ['pulseira/view', 'id' => $model->triagem->pulseira->id],
                                ['class' => 'text-success fw-bold']
                        )
                        : '-' ?>
            </div>
        </div>
    </div>

    <!-- ======================== -->
    <!-- OBSERVAÇÕES -->
    <!-- ======================== -->
    <div class="mb-4">
        <div class="section-title">
            <i class="bi bi-journal-text"></i> Observações
        </div>

        <div class="p-3 rounded border bg-light">
            <?= nl2br($model->observacoes ?: "<span class='text-muted'>Nenhuma observação.</span>") ?>
        </div>
    </div>

    <!-- ======================== -->
    <!-- PRESCRIÇÕES -->
    <!-- ======================== -->
    <div>
        <div class="section-title">
            <i class="bi bi-capsule-pill"></i> Prescrições
        </div>

        <?php if (empty($model->prescricaos)): ?>

            <p class="text-muted">Nenhuma prescrição disponível.</p>

        <?php else: ?>

            <?php foreach ($model->prescricaos as $p): ?>
                <div class="border rounded p-3 mb-2 d-flex justify-content-between">
                    <div>
                        <strong>Prescrição #<?= $p->id ?></strong><br>
                        <?= $p->tipo ?? 'Sem tipo' ?>
                    </div>

                    <?= Html::a('Ver', ['prescricao/view', 'id' => $p->id],
                            ['class' => 'btn btn-outline-success btn-sm']) ?>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

</div>
