<?php
/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $manchester */
/** @var array $evolucaoLabels */
/** @var array $evolucaoData */
/** @var array $pacientes */
/** @var array $ultimas */

use yii\helpers\Html;

$this->title = 'EmergencySTS | Dashboard';

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/sidebar.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/dashboard/admin.css');

$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_END]);

$this->registerJs('

// DONUT
const donut = document.getElementById("chartManchester");
if (donut) {
    new Chart(donut, {
        type: "doughnut",
        data: {
            labels: ["Vermelho","Laranja","Amarelo","Verde","Azul"],
            datasets: [{
                data: [
                    '.$manchester['vermelho'].',
                    '.$manchester['laranja'].',
                    '.$manchester['amarelo'].',
                    '.$manchester['verde'].',
                    '.$manchester['azul'].'
                ],
                backgroundColor: ["#dc3545","#fd7e14","#ffc107","#198754","#0d6efd"]
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: "bottom" }},
            cutout: "65%"
        }
    });

// LINHA – EVOLUÇÃO DAS TRIAGENS
const line = document.getElementById("chartEvolucao");
let triagemChart = null;

if (line) {
    triagemChart = new Chart(line, {
        type: "line",
        data: {
            labels: '.json_encode($evolucaoLabels).',
            datasets: [{
                label: "Triagens",
                data: '.json_encode($evolucaoData).',
                tension: 0.3,
                borderColor: "#198754",
                backgroundColor: "rgba(25,135,84,0.15)",
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: "#198754",
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }},
            scales: {
                y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
            }
        }
    }
}
    });
}
');
?>

<!--HTML DO DASHBOARD-->

<div class="dashboard-wrap">

    <div class="topbar mb-4">
        <div class="brand">
            <i class="bi bi-heart-pulse-fill"></i>
            <span>EmergencySTS</span>
        </div>
    </div>

    <!-- GRÁFICOS -->
    <div class="row g-3 mb-4">

        <div class="col-lg-4">
            <div class="card shadow-sm p-3 h-100">
                <h6 class="mb-3">Prioridades Manchester</h6>
                <canvas id="chartManchester" height="220"></canvas>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm p-3 h-100">
                <h6 class="mb-3">Evolução das Triagens</h6>

                <form class="filter-box mb-3" method="get">
                    <input type="date" name="dataFiltro" class="filter-input"
                           value="<?= Yii::$app->request->get('dataFiltro') ?>">
                    <button class="filter-btn-premium"><i class="bi bi-search"></i> Filtrar</button>
                </form>

                <canvas id="chartEvolucao" height="220"></canvas>
            </div>
        </div>

    </div>

    <!-- Tabela Pacientes -->
    <div class="card shadow-sm p-3 table-modern mb-4">
        <h6 class="mb-3">Pacientes em Espera</h6>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$pacientes): ?>
                    <tr><td colspan="4" class="text-center text-muted">Nenhum registo encontrado</td></tr>
                <?php else: foreach ($pacientes as $p): ?>
                    <tr>
                        <td><?= $p['pulseira']['codigo'] ?></td>
                        <td><?= $p['userprofile']['nome'] ?></td>
                        <td><?= $p['motivoconsulta'] ?></td>
                        <td><?= $p['pulseira']['status'] ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Últimas triagens -->
    <div class="card shadow-sm p-3">
        <h6 class="mb-3">Últimas Triagens</h6>

        <div class="row row-cols-1 row-cols-md-2 g-3">
            <?php if (!$ultimas): ?>
                <p class="text-muted">Nenhuma triagem recente.</p>
            <?php else:
                foreach ($ultimas as $u): ?>
                    <div class="col">
                        <div class="p-3 border rounded-4 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">
                                    <?= date("d/m H:i", strtotime($u['datatriagem'])) ?>
                                    — <?= $u['userprofile']['nome'] ?>
                                </div>
                                <div class="text-muted small"><?= $u['pulseira']['codigo'] ?></div>
                            </div>
                            <div>
                                <span class="badge bg-success"><?= $u['pulseira']['prioridade'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
        </div>
    </div>
</div>
