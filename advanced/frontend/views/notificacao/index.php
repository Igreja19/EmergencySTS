<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Notificacao[] $naoLidas */
/** @var common\models\Notificacao[] $lidas */
/** @var int $kpiNaoLidas */
/** @var int $kpiHoje */
/** @var int $kpiTotal */

$this->title = 'Notificações';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="notificacao-index container py-4">

    <h4 class="fw-semibold mb-1">Notificações</h4>
    <p class="text-muted mb-4">Alertas e atualizações sobre o seu atendimento</p>

    <!-- Estatísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center py-3">
                <h6 class="text-muted mb-1">Não Lidas</h6>
                <h4 class="fw-bold text-danger"><?= $kpiNaoLidas ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center py-3">
                <h6 class="text-muted mb-1">Hoje</h6>
                <h4 class="fw-bold text-success"><?= $kpiHoje ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center py-3">
                <h6 class="text-muted mb-1">Total</h6>
                <h4 class="fw-bold text-primary"><?= $kpiTotal ?></h4>
            </div>
        </div>
    </div>

    <!-- Botão geral -->
    <div class="d-flex justify-content-end mb-3">
        <?= Html::a('Marcar todas como lidas', ['notificacao/marcar-todas-como-lidas'], [
            'class' => 'btn btn-outline-success rounded-pill px-4 fw-semibold',
            'data-confirm' => 'Tem a certeza que pretende marcar todas como lidas?'
        ]) ?>
    </div>

    <!-- Lista de notificações -->
    <div class="lista-notificacoes">

        <?php if (!empty($naoLidas)): ?>
            <h6 class="fw-semibold text-danger mb-3">Novas Notificações</h6>
            <?php foreach ($naoLidas as $n): ?>
                <?php
                switch ($n->tipo) {
                    case 'Consulta':
                        $badgeClass = 'bg-success';
                        $icon = 'bi-check-circle';
                        break;
                    case 'Prioridade':
                        $badgeClass = 'bg-warning text-dark';
                        $icon = 'bi-exclamation-circle';
                        break;
                    default:
                        $badgeClass = 'bg-primary';
                        $icon = 'bi-info-circle';
                        break;
                }
                ?>
                <div class="card shadow-sm border-0 rounded-4 mb-3 bg-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi <?= $icon ?> fs-3 text-secondary"></i>
                                <div>
                                    <h6 class="mb-1 fw-semibold">
                                        <?= Html::encode($n->titulo ?: $n->tipo) ?>
                                        <span class="badge bg-primary-subtle text-primary border ms-1">Nova</span>
                                        <span class="badge <?= $badgeClass ?> ms-1"><?= Html::encode($n->tipo) ?></span>
                                    </h6>
                                    <p class="mb-1 text-muted"><?= Html::encode($n->mensagem) ?></p>
                                    <small class="text-secondary">
                                        <?= Yii::$app->formatter->asRelativeTime($n->dataenvio) ?>
                                    </small>
                                </div>
                            </div>
                            <!-- Botão individual -->
                            <?= Html::a('<i class="bi bi-check2-circle me-1"></i> Marcar como lida',
                                ['notificacao/marcar-como-lida', 'id' => $n->id],
                                ['class' => 'btn btn-sm btn-outline-success rounded-pill px-3']
                            ) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($lidas)): ?>
            <h6 class="fw-semibold text-secondary mt-4 mb-3">Notificações Anteriores</h6>
            <?php foreach ($lidas as $n): ?>
                <?php
                switch ($n->tipo) {
                    case 'Consulta':
                        $badgeClass = 'bg-success';
                        $icon = 'bi-check-circle';
                        break;
                    case 'Prioridade':
                        $badgeClass = 'bg-warning text-dark';
                        $icon = 'bi-exclamation-circle';
                        break;
                    default:
                        $badgeClass = 'bg-primary';
                        $icon = 'bi-info-circle';
                        break;
                }
                ?>
                <div class="card shadow-sm border-0 rounded-4 mb-3">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi <?= $icon ?> fs-3 text-secondary"></i>
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    <?= Html::encode($n->titulo ?: $n->tipo) ?>
                                    <span class="badge <?= $badgeClass ?> ms-1"><?= Html::encode($n->tipo) ?></span>
                                </h6>
                                <p class="mb-1 text-muted"><?= Html::encode($n->mensagem) ?></p>
                                <small class="text-secondary">
                                    <?= Yii::$app->formatter->asRelativeTime($n->dataenvio) ?>
                                </small>
                            </div>
                        </div>
                        <i class="bi bi-check-circle-fill text-success fs-4 ms-md-3 mt-2 mt-md-0"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .notificacao-index {
        max-width: 900px;
        margin: 0 auto;
    }
    .card {
        transition: all 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 10px;
    }
    .bg-primary-subtle {
        background-color: #eaf1ff !important;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .btn-outline-success {
        border: 1px solid #198754;
        color: #198754;
        font-weight: 500;
    }
    .btn-outline-success:hover {
        background-color: #198754;
        color: #fff;
    }
    .shadow-sm {
        box-shadow: 0 1px 3px rgba(0,0,0,0.08)!important;
    }
    h4, h6 { color: #222; }
    .lista-notificacoes .card { border-left: 4px solid transparent; }
    .lista-notificacoes .card.bg-light { border-left: 4px solid #0d6efd; }
</style>
