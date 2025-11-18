<?php
/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $manchester */
/** @var array $evolucaoLabels */
/** @var array $evolucaoData */
/** @var array $pacientes */
/** @var array $ultimas */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'EmergencySTS | Dashboard';

// Bootstrap Icons + CSS global
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/site/index.css');

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

    <div id="chart-config"
         data-url="<?= Url::to(['site/grafico-dados'], true) ?>">
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
<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', [
        'position' => \yii\web\View::POS_END
]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/site/index.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>