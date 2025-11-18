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

// Bootstrap Icons + CSS global
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/sidebar.css');

/* ===== CSS Premium ===== */
$this->registerCss('
body {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}
.dashboard-wrap { padding: 20px; }

.topbar {
  display:flex; align-items:center; justify-content:space-between;
  padding: 14px 20px; border-radius:16px;
  background: rgba(255,255,255,0.8);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(0,0,0,.05);
}

.brand { font-weight:700; color:#198754; display:flex; align-items:center; gap:8px; }

.card-kpi {
  border-radius:20px;
  border:none;
  padding:20px;
  background:#fff;
  box-shadow:0 4px 20px rgba(0,0,0,0.05);
  transition:.2s;
}
.card-kpi:hover { transform:translateY(-3px); }

.card-kpi .icon { font-size:28px; margin-bottom:10px; }
.card-kpi.red .icon { color:#dc3545; }
.card-kpi.orange .icon { color:#fd7e14; }
.card-kpi.green .icon { color:#198754; }
.card-kpi.blue .icon { color:#0d6efd; }

.table-modern { border-radius:16px; overflow:hidden; }
.table-modern thead tr { background:#198754; color:#fff; }
.table-modern tbody tr:hover { background:rgba(25,135,84,0.05); }

.badge-prio { font-weight:600; }
.badge-vermelho { background:#dc3545; }
.badge-laranja  { background:#fd7e14; }
.badge-amarelo  { background:#ffc107; color:#000; }
.badge-verde    { background:#198754; }
.badge-azul     { background:#0d6efd; }

.filter-box { display:flex; gap:14px; align-items:center; }
.filter-input-wrapper { position:relative; }
.filter-input {
  padding:10px 14px 10px 42px;
  border-radius:12px;
  border:1px solid #ced4da;
  height:42px; width:200px;
}
.filter-icon {
  position:absolute; top:50%; left:12px;
  transform:translateY(-50%);
  color:#198754;
}
.filter-btn-premium {
  background:linear-gradient(135deg,#198754,#149e65);
  border:none;
  height:42px;
  padding:0 22px;
  border-radius:12px;
  display:flex; align-items:center; gap:8px;
  color:white; font-weight:600;
}
');

/* ===== Chart.js ===== */
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', [
        'position' => \yii\web\View::POS_END
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
?>

<!-- ====================================== -->
<!--              DASHBOARD                 -->
<!-- ====================================== -->

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
            <div class="card card-kpi red text-center">
                <div class="icon"><i class="bi bi-people-fill"></i></div>
                <div class="value"><?= (int)$stats["espera"] ?></div>
                <div class="label">Pacientes em espera</div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card card-kpi orange text-center">
                <div class="icon"><i class="bi bi-activity"></i></div>
                <div class="value"><?= (int)$stats["ativas"] ?></div>
                <div class="label">Triagens ativas</div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card card-kpi green text-center">
                <div class="icon"><i class="bi bi-heart-pulse"></i></div>
                <div class="value"><?= (int)$stats["atendidosHoje"] ?></div>
                <div class="label">Atendidos hoje</div>
            </div>
        </div>
    </div>

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

    <!-- Tabela Pacientes -->
    <div class="card shadow-sm p-3 table-modern mb-4">
        <h6 class="mb-3">Pacientes em Espera</h6>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                </tr>
                </thead>

                <tbody>
                <?php if (empty($pacientes)): ?>
                    <tr><td colspan="4" class="text-center text-muted">Nenhum registo encontrado</td></tr>
                <?php else: foreach ($pacientes as $p): ?>
                    <tr>
                        <td><?= Html::encode($p["pulseira"]["codigo"] ?? "-") ?></td>
                        <td><?= Html::encode($p["userprofile"]["nome"] ?? "-") ?></td>
                        <td><?= Html::encode($p["motivoconsulta"] ?? "-") ?></td>
                        <td><?= Html::encode($p["pulseira"]["status"] ?? "-") ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

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

</div>
