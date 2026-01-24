<?php
/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $manchester */
/** @var array $evolucaoLabels */
/** @var array $evolucaoData */
/** @var array $pacientes */
/** @var array $ultimas */

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\View;
use common\helpers\UserAgentHelper;

$this->title = 'EmergencySTS | Dashboard';

// Bootstrap Icons + CSS global
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/site/index.css');

/* ===== Chart.js ===== */
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', [
        'position' => View::POS_END
]);


/* ===== GRÁFICOS ===== */
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
        options: { plugins:{ legend:{ position:"bottom" } } }
    });
}

// LINHA — com inteiros + eixo Y corrigido
const line = document.getElementById("chartEvolucao");
let triagemChart = null;

if (line) {
    triagemChart = new Chart(line, {
        type: "line",
        data: {
            labels: '.json_encode($evolucaoLabels).',
            datasets: [{
                label: "Triagens",
                data: '.json_encode(array_map("intval", $evolucaoData)).',
                tension: .35,
                borderColor: "#198754",
                backgroundColor: "rgba(25,135,84,0.1)",
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: "#198754"
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero:true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : "";
                        }
                    }
                }
            }
        }
    });
}
');

// Badge helper
function badgePrio(string $prio): string {
    $map = [
            "Vermelho"=>"badge-vermelho",
            "Laranja"=>"badge-laranja",
            "Amarelo"=>"badge-amarelo",
            "Verde"=>"badge-verde",
            "Azul"=>"badge-azul"
    ];
    $cls = $map[$prio] ?? "bg-secondary";
    return "<span class=\"badge badge-prio {$cls}\">{$prio}</span>";
}

if ($isMedico && !$isAdmin) {
    $urlDestino = ['/consulta/create'];
} elseif ($isEnfermeiro && !$isAdmin) {
    $urlDestino = ['/triagem/index'];
} elseif ($isAdmin) {
    $urlDestino = ['/triagem/index'];
}
?>

<!--DASHBOARD-->

<div class="dashboard-wrap">

    <div class="topbar mb-4">
        <div class="brand">
            <i class="bi bi-heart-pulse-fill"></i>
            <span>EmergencySTS</span>
        </div>
    </div>

        <!-- KPIs -->
        <div class="row g-3 mb-4 justify-content-center">
            <div class="col-lg-3 col-sm-6">
                <a href="<?= Url::to($urlDestino) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card card-kpi red text-center">
                        <div class="icon"><i class="bi bi-people-fill"></i></div>
                        <div class="value"><?= (int)$stats["espera"] ?></div>
                        <div class="label">Pacientes em espera</div>
                    </div>
                </a>
            </div>
            <?php if ($isAdmin): ?>
                <div class="col-lg-3 col-sm-6">
                    <a href="<?= Url::to(['/pulseira/index']) ?>" style="text-decoration: none; color: inherit;">
                        <div class="card card-kpi orange text-center">
                            <div class="icon"><i class="bi bi-activity"></i></div>
                            <div class="value"><?= (int)$stats["ativas"] ?></div>
                            <div class="label">Triagens ativas</div>
                        </div>
                    </a>
                </div>
            <?php endif ?>
            <?php if ($isAdmin): ?>
                <div class="col-lg-3 col-sm-6">
                    <a href="<?= Url::to(['/user-profile/index']) ?>" style="text-decoration: none; color: inherit;">
                        <div class="card card-kpi orange text-center">
                            <div class="icon"><i class="bi bi-person-badge"></i></div>
                            <div class="value"><?= (int)$stats["totalUtilizadores"] ?></div>
                            <div class="label">Utilizadores</div>
                        </div>
                    </a>
                </div>
            <?php endif ?>
            <?php if ($isMedico || $isAdmin): ?>
                <div class="col-lg-3 col-sm-6">
                    <a href="<?= Url::to(['/consulta/historico']) ?>" style="text-decoration: none; color: inherit;">
                        <div class="card card-kpi green text-center">
                            <div class="icon"><i class="bi bi-heart-pulse"></i></div>
                            <div class="value"><?= (int)$stats["atendidosHoje"] ?></div>
                            <div class="label">Atendidos hoje</div>
                        </div>
                    </a>
                </div>
            <?php endif ?>
        </div>
    <?php if ($isAdmin): ?>
        <!-- GRÁFICOS -->
        <div class="row g-3 mb-4">

            <div class="col-lg-4">
                <div class="card shadow-sm p-3">
                    <h6 class="mb-2">Prioridades Manchester</h6>
                    <canvas id="chartManchester" height="220"></canvas>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm p-3">
                    <h6 class="mb-2">Evolução das Triagens</h6>

                    <form method="get" class="filter-box mb-3">
                        <div class="filter-input-wrapper">
                            <i class="bi bi-calendar2-date filter-icon"></i>
                            <input type="date"
                                   name="dataFiltro"
                                   class="filter-input"
                                   value="<?= Yii::$app->request->get('dataFiltro') ?>">
                        </div>

                        <button class="filter-btn-premium">
                            <i class="bi bi-search"></i>
                            Filtrar
                        </button>
                    </form>

                    <canvas id="chartEvolucao" height="220"></canvas>
                </div>
            </div>
        </div>
    <?php endif ?>
    <?php if ($isAdmin): ?>
        <div class="card shadow-sm p-3 mb-4">
            <h6 class="mb-3">Histórico de Logins</h6>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Data / Hora</th>
                        <th>Utilizador</th>
                        <th>IP</th>
                        <th>Dispositivo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($logins)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Sem registos de login.</td>
                        </tr>
                    <?php else: foreach ($logins as $l): ?>
                        <tr>
                            <td><?= date("d/m/Y H:i", strtotime($l['data_login'])) ?></td>
                            <td><?= Html::encode($l['user']['username'] ?? '-') ?></td>
                            <td><?= Html::encode($l['ip'] ?? '-') ?></td>
                            <td class="small"><?= UserAgentHelper::format($l["user_agent"]) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isEnfermeiro): ?>
        <!-- Últimas Triagens -->
        <div class="card shadow-sm p-3">
            <h6 class="mb-3">Últimas Triagens</h6>

            <div class="row row-cols-1 row-cols-md-2 g-3">

                <?php if (empty($ultimas)): ?>
                    <p class="text-muted">Nenhuma triagem recente.</p>

                <?php else: foreach ($ultimas as $u): ?>
                    <div class="col">
                        <div class="p-3 border rounded-4 d-flex justify-content-between">
                            <div>
                                <div class="fw-semibold">
                                    <?= date("d/m H:i", strtotime($u["datatriagem"])) ?> —
                                    <?= Html::encode($u["userprofile"]["nome"] ?? "-") ?>
                                </div>
                                <div class="text-muted small">
                                    <?= Html::encode($u["pulseira"]["codigo"] ?? "-") ?>
                                </div>
                            </div>

                            <div>
                                <?= badgePrio($u["pulseira"]["prioridade"] ?? "-") ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>

            </div>
        </div>
    <?php endif ?>
</div>
