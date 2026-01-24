<?php
use yii\helpers\Html;

$this->title = 'Painel de Triagem - EmergencySTS';
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/pulseira/index.css');

if (!$pulseira) {
    echo '<div class="container py-5 text-center">
            <div class="alert alert-warning rounded-4 shadow-sm p-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Nenhuma pulseira encontrada.
            </div>' .
            Html::a('<i class="bi bi-arrow-left-circle me-2"></i> Voltar à Triagem', ['triagem/formulario'], [
                    'class' => 'btn btn-success mt-3 px-4 py-2'
            ]) .
            '</div>';
    return;
}

$cores = [
        'Vermelho' => '#dc3545',
        'Laranja'  => '#fd7e14',
        'Amarelo'  => '#ffc107',
        'Verde'    => '#198754',
        'Azul'     => '#0d6efd',
        'Pendente' => '#6c757d',
];

$corBg = $cores[$pulseira->prioridade] ?? '#6c757d';

$corTexto = ($pulseira->prioridade === 'Amarelo') ? '#000000' : '#ffffff';

?>


<div class="container py-5">
    <h5 class="fw-bold text-success mb-2">Tempo de Espera Estimado</h5>
    <p class="text-muted">Consulta do seu estado na fila de atendimento</p>

    <!-- CARD PRINCIPAL -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 main-status-card position-relative">

        <!-- Cabeçalho com número e selo -->
        <div class="d-flex justify-content-between align-items-start position-relative">
            <div>
                <small class="text-muted">O seu número de triagem</small>
                <h2 class="fw-bold m-0"><?= Html::encode($pulseira->codigo) ?></h2>
            </div>

            <!-- Cor da pulseira -->
            <div class="selo position-absolute top-0 end-0 mt-2 me-3 d-flex align-items-center justify-content-center fw-bold text-uppercase"
                 style="background-color: <?= $corBg ?>; color: <?= $corTexto ?>;">
                <?= strtoupper(Html::encode($pulseira->prioridade ?? 'PENDENTE')) ?>
            </div>
        </div>

        <hr class="my-3">

        <!-- Barra de progresso -->
        <div class="mt-3 position-relative">
            <div class="barra progress rounded-pill triage-track" style="height: 14px; overflow: hidden;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" style="
                             width: <?= min(100, (int)$progressPct) ?>%;
                             background: linear-gradient(90deg, <?= $corBg ?>, <?= $corBg ?>cc);
                             box-shadow: 0 0 10px <?= $corBg ?>80;
                             ">
                </div>
            </div>

            <div class="small text-muted text-end mt-1">
                <?= (int)$progressPct ?>%
            </div>
        </div>

        <!-- Legenda da fila -->
        <div class="small text-muted mb-1 mt-3">
            <?php if ($pulseira->prioridade === 'Pendente'): ?>
                A sua prioridade ainda não foi atribuída. Aguarde avaliação de um enfermeiro.
            <?php else: ?>
                Posição <?= (int)$position ?> de <?= (int)$totalAguardarPrioridade ?> na fila de prioridade
                <strong style="background-color: <?= $corBg ?>; color: <?= $corTexto ?>;"><?= Html::encode($pulseira->prioridade) ?></strong>.
            <?php endif; ?>
        </div>

        <!-- Nome do utilizador -->
        <div class="mt-3">
            <span class="text-muted">Utilizador:</span>
            <span class="fw-semibold <?= $utilizadorNome === 'Desconhecido' ? 'text-secondary' : 'text-dark' ?>">
                <?= Html::encode($utilizadorNome ?? 'Desconhecido') ?>
            </span>
        </div>
    </div>

    <!-- FILA -->
    <h6 class="fw-bold text-success mb-3">
        <i class="bi bi-people me-2"></i>Fila de Atendimento
    </h6>

    <div class="list-group mb-4">
        <?php foreach ($fila as $item): ?>
            <?php
            $isMe   = ($item->id === $pulseira->id);
            $corItem= $cores[$item->prioridade] ?? '#6c757d';
            $bgMe   = $isMe ? 'background:#e9f2ff; border:1.5px solid #0d6efd;' : 'background:#f8f9fa;';
            ?>
            <div class="list-group-item d-flex justify-content-between align-items-center rounded-3 mb-2"
                 style="border-left:6px solid <?= $corItem ?>; <?= $bgMe ?>">
                <div>
                    <span class="fw-semibold" style="color: <?= $corItem ?>;">
                        <?= Html::encode($item->codigo) ?>
                    </span>
                    <?php if ($isMe): ?>
                        <span class="ms-1 small text-primary fw-semibold">(Você)</span>
                    <?php endif; ?>
                    <div class="small text-muted"><?= date('H:i', strtotime($item->tempoentrada)) ?></div>
                </div>

                <?php if (strcasecmp($item->status, 'Em atendimento') === 0): ?>
                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2">Em atendimento</span>
                <?php elseif (strcasecmp($item->status, 'Em espera') === 0): ?>
                    <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-2">Em espera</span>
                <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary px-3 py-2">Atendido</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ESTATÍSTICAS -->
    <div class="row g-3 text-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 py-3">
                <div class="fw-bold fs-4"><?= (int)$totalAguardar ?></div>
                <div class="text-muted small">Utilizadores a Aguardar</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 py-3">
                <div class="fw-bold fs-4"><?= (int)$tempoMedio ?> min</div>
                <div class="text-muted small">Tempo Médio de Espera</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 py-3">
                <div class="fw-bold fs-5"><?= Html::encode($afluencia) ?></div>
                <div class="text-muted small">Nível de Afluência</div>
            </div>
        </div>
    </div>
</div>