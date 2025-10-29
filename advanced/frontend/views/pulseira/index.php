<?php
use yii\helpers\Html;

$this->title = 'Painel de Triagem - EmergencySTS';

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_END]);

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

// 🔹 Cores das prioridades
$cores = [
        'Vermelha' => '#dc3545',
        'Laranja'  => '#fd7e14',
        'Amarela'  => '#ffc107',
        'Verde'    => '#198754',
        'Azul'     => '#0d6efd',
];
$cor = $cores[$pulseira->prioridade] ?? '#198754';

// 🔹 Ajuste do tempo estimado
if ($tempoEstimadoMin <= 0 && $position > 1) {
    $tempoEstimadoMin = max(5, $position * 5);
}

// 🔹 Corrige progresso
if ($position > 1 && $totalAguardar > 0) {
    $progressPct = max(0, 100 - (($position - 1) / $totalAguardar) * 100);
} else {
    $progressPct = 100;
}
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

            <!-- Selo da cor -->
            <div class="position-absolute top-0 end-0 mt-2 me-3 d-flex align-items-center justify-content-center fw-bold text-uppercase"
                 style="
                         background-color: <?= $cor ?>;
                         color: #fff;
                         font-size: 0.9rem;
                         padding: 0.4rem 1rem;
                         border-radius: 6px;
                         min-width: 90px;
                         height: 30px;
                         letter-spacing: .5px;
                         box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                         ">
                <?= strtoupper(Html::encode($pulseira->prioridade)) ?>
            </div>
        </div>

        <hr class="my-3">

        <!-- Informações principais -->
        <div class="row text-center g-3">
            <div class="col-md-4">
                <p class="text-muted mb-1">Tempo Decorrido</p>
                <h6 class="fw-semibold"><?= (int)$tempoDecorridoMin ?> min</h6>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1">Tempo Estimado</p>
                <h6 class="fw-semibold">~<?= (int)$tempoEstimadoMin ?> min</h6>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1">Posição na Fila</p>
                <h6 class="fw-semibold"><?= (int)$position ?>º</h6>
            </div>
        </div>

        <!-- Barra de progresso -->
        <div class="mt-3 position-relative">
            <div class="progress rounded-pill triage-track" style="height: 12px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated"
                     style="width: <?= min(100, (int)$progressPct) ?>%; background-color: <?= $cor ?>;">
                </div>
            </div>
            <div class="small text-muted text-end mt-1"><?= (int)$progressPct ?>%</div>
        </div>

        <div class="small text-muted mb-1 mt-3">
            Progresso do tempo máximo (<?= isset($maxByPriority[$pulseira->prioridade]) ? (int)$maxByPriority[$pulseira->prioridade] : 60 ?> min)
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
                <?php elseif (strcasecmp($item->status, 'Aguardando') === 0): ?>
                    <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-2">Aguardando</span>
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

<?php
    $this->registerCss(<<<CSS
    body {
        background: linear-gradient(180deg, #f7fff9 0%, #f6f9ff 100%);
        font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .main-status-card {
        background: #eef6ff;
        border: 1px solid #dbe9ff;
    }
    .triage-track {
        background: linear-gradient(90deg, rgba(25,135,84,.15) 0%, rgba(25,135,84,0) 100%);
    }
    .progress-bar { transition: width .8s ease; }
    .list-group-item { transition: all .2s ease; }
    .list-group-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0,0,0,.06);
    }
    CSS);
?>
