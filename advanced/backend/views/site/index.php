<?php
/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $manchester */
/** @var array $evolucaoLabels */
/** @var array $evolucaoData */
/** @var array $pacientes */
/** @var array $ultimas */
/** @var array $notificacoes */

use yii\helpers\Html;

$this->title = 'EmergencySTS | Dashboard';

// Bootstrap Icons
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');

/* ===== CSS Premium ===== */
$this->registerCss('
body {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  transition: background 0.4s ease, color 0.4s ease;
}
.dark body, .dark {
  background: linear-gradient(135deg, #0b0e11 0%, #151a1f 100%);
  color: #dfe6ee;
}
.dashboard-wrap { padding: 20px; }

.topbar {
  display:flex; align-items:center; justify-content:space-between;
  padding: 14px 20px; border-radius:16px;
  background: rgba(255,255,255,0.8); backdrop-filter: blur(8px);
  border: 1px solid rgba(0,0,0,.05);
  transition: background 0.3s ease;
}
.dark .topbar { background: rgba(20,25,30,0.7); border-color: rgba(255,255,255,.08); }

.brand { font-weight:700; color:#198754; display:flex; align-items:center; gap:8px; }
.btn-ghost { background:transparent; border:none; font-size:20px; transition:transform .2s; }
.btn-ghost:hover { transform: scale(1.15); }
.notif-dot::after {
  content:""; position:absolute; right:-3px; top:-3px;
  width:10px; height:10px; background:#dc3545; border-radius:50%;
}
.card-kpi {
  border-radius:20px; border:none; padding:20px;
  background: #fff; box-shadow:0 4px 20px rgba(0,0,0,0.05);
  transition:transform .2s, box-shadow .3s;
}
.card-kpi:hover { transform:translateY(-3px); box-shadow:0 6px 24px rgba(0,0,0,0.08); }
.dark .card-kpi { background:#141a1f; }

.card-kpi .icon {
  font-size:28px; color:#198754; margin-bottom:10px;
}
.card-kpi.red .icon { color:#dc3545; }
.card-kpi.orange .icon { color:#fd7e14; }
.card-kpi.green .icon { color:#198754; }
.card-kpi.blue .icon { color:#0d6efd; }
.card-kpi .value { font-size:30px; font-weight:700; }
.card-kpi .label { color:#6c757d; }

.table-modern { border-radius:16px; overflow:hidden; }
.table-modern thead tr { background:#198754; color:#fff; }
.table-modern tbody tr:hover { background:rgba(25,135,84,0.05); }
.dark .table-modern thead tr { background:#146c43; }

.badge-prio { font-weight:600; }
.badge-vermelho { background:#dc3545; }
.badge-laranja  { background:#fd7e14; }
.badge-amarelo  { background:#ffc107; color:#000; }
.badge-verde    { background:#198754; }
.badge-azul     { background:#0d6efd; }

.transition { transition: all 0.3s ease; }
');

$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_END]);

/* ===== JS atualizado com eixo Y inteiro e animação ===== */
$this->registerJs('
const root=document.documentElement;
const key="emergencysts-theme";
if(localStorage.getItem(key)==="dark"){ root.classList.add("dark"); }
document.getElementById("toggle-dark")?.addEventListener("click",()=>{
  root.classList.toggle("dark");
  localStorage.setItem(key, root.classList.contains("dark")?"dark":"light");
});

const donut=document.getElementById("chartManchester");
if(donut){
  new Chart(donut,{
    type:"doughnut",
    data:{
      labels:["Vermelho","Laranja","Amarelo","Verde","Azul"],
      datasets:[{
        data:[
          '.$manchester['vermelho'].',
          '.$manchester['laranja'].',
          '.$manchester['amarelo'].',
          '.$manchester['verde'].',
          '.$manchester['azul'].'
        ],
        backgroundColor:["#dc3545","#fd7e14","#ffc107","#198754","#0d6efd"]
      }]
    },
    options:{
      responsive:true,
      plugins:{ legend:{ position:"bottom" } },
      cutout:"65%",
      animation:{
        animateScale:true,
        animateRotate:true,
        duration:1200
      }
    }
  });
}

const line=document.getElementById("chartEvolucao");
if(line){
  new Chart(line,{
    type:"line",
    data:{
      labels:'.json_encode($evolucaoLabels).',
      datasets:[{
        label:"Triagens",
        data:'.json_encode($evolucaoData).',
        tension:.35,
        borderColor:"#198754",
        backgroundColor:"rgba(25,135,84,0.1)",
        fill:true,
        pointRadius:4,
        pointBackgroundColor:"#198754"
      }]
    },
    options:{
      responsive:true,
      plugins:{ legend:{ display:false } },
      scales:{
        y:{
          beginAtZero:true,
          ticks:{
            stepSize:1,
            precision:0
          },
          title:{
            display:true,
            text:"Número de Triagens"
          }
        },
        x:{
          title:{
            display:true,
            text:"Dias"
          }
        }
      },
      elements:{
        line:{ borderWidth:2 },
        point:{ radius:4 }
      },
      animation:{
        duration:1500,
        easing:"easeOutQuart"
      }
    }
  });
}
', \yii\web\View::POS_END);

/* Helper para badges de prioridade */
function badgePrio(string $prio): string {
    $map = [
            'Vermelho'=>'badge-vermelho', 'Laranja'=>'badge-laranja', 'Amarelo'=>'badge-amarelo',
            'Verde'=>'badge-verde', 'Azul'=>'badge-azul'
    ];
    $cls = $map[$prio] ?? 'bg-secondary';
    return "<span class=\"badge badge-prio {$cls}\">{$prio}</span>";
}
?>

<div class="dashboard-wrap">

    <!-- 🔹 Topbar -->
    <div class="topbar mb-4">
        <div class="brand">
            <i class="bi bi-heart-pulse-fill"></i>
            <span>EmergencySTS</span>
        </div>
        <div class="actions d-flex gap-3">
            <button id="toggle-dark" class="btn-ghost" title="Modo escuro/claro"><i class="bi bi-moon"></i></button>
            <div class="dropdown position-relative">
                <button class="btn-ghost position-relative notif-dot" data-bs-toggle="dropdown"><i class="bi bi-bell"></i></button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header">Notificações</h6></li>
                    <?php if (empty($notificacoes)): ?>
                        <li><span class="dropdown-item-text text-muted">Sem novas notificações</span></li>
                    <?php else: foreach ($notificacoes as $n): ?>
                        <li><span class="dropdown-item-text"><?= Html::encode($n["titulo"]) ?> — <?= Html::encode($n["mensagem"]) ?></span></li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- 🔹 KPIs -->
    <div class="row g-3 mb-4 justify-content-center">
        <div class="col-12 col-sm-6 col-lg-3 transition">
            <div class="card card-kpi red text-center">
                <div class="icon"><i class="bi bi-people-fill"></i></div>
                <div class="value"><?= (int)$stats["espera"] ?></div>
                <div class="label">Pacientes em espera</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 transition">
            <div class="card card-kpi orange text-center">
                <div class="icon"><i class="bi bi-activity"></i></div>
                <div class="value"><?= (int)$stats["ativas"] ?></div>
                <div class="label">Triagens ativas</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 transition">
            <div class="card card-kpi green text-center">
                <div class="icon"><i class="bi bi-heart-pulse"></i></div>
                <div class="value"><?= (int)$stats["atendidosHoje"] ?></div>
                <div class="label">Atendidos hoje</div>
            </div>
        </div>
    </div>

    <!-- 🔹 Gráficos -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm p-3 h-100" style="border-radius:16px;">
                <h6 class="mb-2"><i class="bi bi-palette me-1"></i> Prioridades Manchester</h6>
                <canvas id="chartManchester" height="220"></canvas>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm p-3 h-100" style="border-radius:16px;">
                <h6 class="mb-2"><i class="bi bi-graph-up-arrow me-1"></i> Evolução das Triagens</h6>
                <canvas id="chartEvolucao" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- 🔹 Tabela -->
    <div class="card shadow-sm p-3 table-modern mb-4" style="border-radius:16px;">
        <h6 class="mb-3"><i class="bi bi-list-check me-1"></i> Pacientes em Triagem</h6>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Motivo</th>
                    <th>Prioridade</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($pacientes)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Nenhum registo encontrado</td></tr>
                <?php else: foreach ($pacientes as $p): ?>
                    <tr>
                        <td><?= Html::encode($p["pulseira"]["codigo"] ?? "-") ?></td>
                        <td><?= Html::encode($p["userprofile"]["nome"] ?? "-") ?></td>
                        <td><?= Html::encode($p["motivoconsulta"] ?? "-") ?></td>
                        <td><?= badgePrio($p["pulseira"]["prioridade"] ?? "-") ?></td>
                        <td><?= Html::encode($p["pulseira"]["status"] ?? "-") ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 🔹 Últimas triagens -->
    <div class="card shadow-sm p-3" style="border-radius:16px;">
        <h6 class="mb-3"><i class="bi bi-clock-history me-1"></i> Últimas Triagens</h6>
        <div class="row row-cols-1 row-cols-md-2 g-3">
            <?php if (empty($ultimas)): ?>
                <p class="text-muted">Nenhuma triagem recente.</p>
            <?php else: foreach ($ultimas as $u): ?>
                <div class="col">
                    <div class="p-3 border rounded-4 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold"><?= date("d/m H:i", strtotime($u["datatriagem"])) ?> — <?= Html::encode($u["userprofile"]["nome"] ?? "-") ?></div>
                            <div class="text-muted small"><?= Html::encode($u["pulseira"]["codigo"] ?? "-") ?></div>
                        </div>
                        <div><?= badgePrio($u["pulseira"]["prioridade"] ?? "-") ?></div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
