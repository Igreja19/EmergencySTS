<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Consulta $consulta */
/** @var common\models\Triagem|null $triagem */

$this->title = 'Consulta #' . $consulta->id;
$this->params['breadcrumbs'][] = ['label' => 'Histórico de Consultas', 'url' => ['historico']];
$this->params['breadcrumbs'][] = $this->title;

$triagem = $triagem ?? $consulta->triagem ?? null;

// Badge por prioridade (cores Bootstrap)
$prio = $consulta->prioridade;
$badgeClass = match ($prio) {
    'Vermelho' => 'bg-danger',
    'Laranja'  => 'bg-warning text-dark',
    'Amarelo'  => 'bg-warning text-dark',
    'Verde'    => 'bg-success',
    'Azul'     => 'bg-primary',
    default    => 'bg-secondary',
};

?>
<div class="container py-4 consulta-ver">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">
            Consulta #<?= Html::encode($consulta->id) ?>
            <?php if ($prio): ?>
                <span class="badge <?= $badgeClass ?> align-middle ms-2">
                    <?= Html::encode($prio) ?>
                </span>
            <?php endif; ?>
            <span class="badge bg-secondary align-middle ms-1">
                <?= Html::encode($consulta->estado) ?>
            </span>
        </h4>

        <?= Html::a('← Voltar ao Histórico', ['historico'], ['class' => 'btn btn-outline-secondary rounded-pill']) ?>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="small text-muted">Data da Consulta</div>
                    <div class="fw-semibold">
                        <?= Yii::$app->formatter->asDatetime($consulta->data_consulta, 'php:d/m/Y H:i') ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Motivo</div>
                    <div class="fw-semibold"><?= Html::encode($consulta->motivo ?: '—') ?></div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Triagem</div>
                    <div class="fw-semibold">
                        <?= $triagem ? ('#' . Html::encode($triagem->id)) : '—' ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Paciente</div>
                    <div class="fw-semibold">
                        <?= Html::encode($consulta->paciente->nomecompleto ?? ('ID '.$consulta->userprofile_id)) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Profissional</div>
                    <div class="fw-semibold">
                        <?= Html::encode($consulta->userprofile->username ?? ('ID '.$consulta->userprofile_id)) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Encerramento</div>
                    <div class="fw-semibold">
                        <?= $consulta->data_encerramento
                            ? Yii::$app->formatter->asDatetime($consulta->data_encerramento, 'php:d/m/Y H:i')
                            : '—' ?>
                    </div>
                </div>

                <?php if (!empty($consulta->tempo_consulta)): ?>
                    <div class="col-md-4">
                        <div class="small text-muted">Tempo de Consulta</div>
                        <div class="fw-semibold"><?= Html::encode($consulta->tempo_consulta) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($consulta->relatorio_pdf)): ?>
                    <div class="col-md-8">
                        <div class="small text-muted">Relatório</div>
                        <div class="fw-semibold">
                            <?= Html::a('Download do Relatório', $consulta->relatorio_pdf, ['class' => 'link-primary', 'target' => '_blank']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="small text-muted mb-1">Observações</div>
            <div><?= nl2br(Html::encode($consulta->observacoes ?: 'Sem observações.')) ?></div>
        </div>
    </div>
    <br>
    <?= Html::a('<i class="bi bi-file-earmark-pdf"></i> Gerar PDF',
            ['consulta/pdf', 'id' => $consulta->id],
            ['class' => 'btn btn-danger rounded-pill']) ?>
</div>
<style>
    .consulta-ver .card { transition: all .2s ease-in-out; }
    .consulta-ver .card:hover { transform: translateY(-1px); }
    .badge { border-radius: 10px; }
</style>
