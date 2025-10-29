<?php
use yii\helpers\Html;

/** @var common\models\Consulta $consulta */
/** @var common\models\Triagem|null $triagem */

$triagem = $triagem ?? $consulta->triagem ?? null;

// Caminho do logo (mPDF aceita caminho absoluto do filesystem ou URL pública)
$logoPath = \Yii::getAlias('@frontend/web/img/logo.png');

// Classes de cor para prioridade
$prio = $consulta->prioridade;
$prioColor = match ($prio) {
    'Vermelho' => '#dc3545',
    'Laranja'  => '#fd7e14',
    'Amarelo'  => '#ffc107',
    'Verde'    => '#198754',
    'Azul'     => '#0d6efd',
    default    => '#6c757d',
};
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Relatório da Consulta #<?= Html::encode($consulta->id) ?></title>
    <style>
        /* ======= Reset/Helpers ======= */
        * { box-sizing: border-box; }
        html, body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#1f2937; font-size:12px; line-height:1.45; }
        h1,h2,h3,h4 { margin:0 0 6px 0; }
        .muted { color:#6b7280; }
        .small { font-size: 11px; color:#6b7280; }
        .pill { display:inline-block; padding:3px 8px; border-radius:999px; font-weight:600; font-size:11px; color:#fff; }
        .card { border:1px solid #e5e7eb; border-radius:12px; padding:14px; margin-bottom:12px; }
        .row { display:flex; gap:12px; }
        .col { flex:1; }
        .divider { height:1px; background:#e5e7eb; margin:12px 0; }
        .k { color:#6b7280; font-size:11px; margin-bottom:3px; text-transform:uppercase; letter-spacing:.04em; }
        .v { font-weight:600; }
        .section-title { font-size:13px; font-weight:700; color:#111827; margin-bottom:8px; }
        .table { width:100%; border-collapse: collapse; }
        .table th, .table td { padding:8px 10px; border:1px solid #e5e7eb; vertical-align: top; }
        .table th { background:#f9fafb; text-align:left; font-weight:700; }
        .badge-prio { background: <?= $prioColor ?>; color:#fff; }
        .badge-state { background:#6b7280; color:#fff; }
        .header { display:flex; align-items:center; justify-content:space-between; padding:10px 0 14px; border-bottom:2px solid #10b981; }
        .brand { display:flex; align-items:center; gap:10px; }
        .brand-logo { width:40px; height:40px; object-fit:contain; }
        .brand-title { font-size:18px; font-weight:800; color:#10b981; letter-spacing:.3px; }
        .meta { text-align:right; }
        .meta .title { font-size:16px; font-weight:700; color:#111827; }
        .footer { text-align:center; color:#6b7280; font-size:10px; margin-top:12px; }
        /* mPDF footer page number via CSS content (apenas indicativo; para paginação avançada usar SetHTMLFooter no controller) */
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="brand">
        <?php if (is_file($logoPath)): ?>
            <img src="<?= $logoPath ?>" class="brand-logo" alt="Logo" style="width:80px; height:auto;">
        <?php endif; ?>
        <div class="brand-title">EmergencySTS</div>
    </div>
    <div class="meta">
        <div class="title">Relatório da Consulta #<?= Html::encode($consulta->id) ?></div>
        <div class="small">Gerado em <?= date('d/m/Y H:i') ?></div>
    </div>
</div>

<!-- CAPA/RESUMO -->
<div class="card" style="margin-top:12px;">
    <div class="row">
        <div class="col">
            <div class="k">Paciente</div>
            <div class="v"><?= Html::encode($consulta->paciente->nomecompleto ?? ('ID '.$consulta->userprofile_id)) ?></div>
        </div>
        <div class="col">
            <div class="k">Profissional</div>
            <div class="v"><?= Html::encode($consulta->userprofile->username ?? ('ID '.$consulta->userprofile_id)) ?></div>
        </div>
        <div class="col">
            <div class="k">Data da Consulta</div>
            <div class="v"><?= Yii::$app->formatter->asDatetime($consulta->data_consulta, 'php:d/m/Y H:i') ?></div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="row">
        <div class="col">
            <div class="k">Estado</div>
            <span class="pill badge-state"><?= Html::encode($consulta->estado ?? '—') ?></span>
        </div>
        <div class="col">
            <div class="k">Prioridade</div>
            <span class="pill badge-prio"><?= Html::encode($consulta->prioridade ?? '—') ?></span>
        </div>
        <div class="col">
            <div class="k">Triagem</div>
            <div class="v">#<?= Html::encode($consulta->triagem_id ?? ($triagem->id ?? '—')) ?></div>
        </div>
    </div>
</div>

<!-- MOTIVO & OBSERVAÇÕES -->
<div class="card">
    <div class="section-title">Resumo Clínico</div>
    <table class="table">
        <tr>
            <th style="width:30%;">Motivo da Consulta</th>
            <td><?= nl2br(Html::encode($consulta->motivo ?: '—')) ?></td>
        </tr>
        <tr>
            <th>Observações</th>
            <td><?= nl2br(Html::encode($consulta->observacoes ?: 'Sem observações.')) ?></td>
        </tr>
        <tr>
            <th>Encerramento</th>
            <td>
                <?= $consulta->data_encerramento
                        ? Yii::$app->formatter->asDatetime($consulta->data_encerramento, 'php:d/m/Y H:i')
                        : '—' ?>
                <?php if (!empty($consulta->tempo_consulta)): ?>
                    &nbsp; <span class="small">(Duração: <?= Html::encode($consulta->tempo_consulta) ?>)</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<!-- DADOS DA TRIAGEM (se existirem) -->
<?php if ($triagem): ?>
    <div class="card">
        <div class="section-title">Detalhes da Triagem</div>
        <table class="table">
            <tr>
                <th style="width:30%;">Queixa Principal</th>
                <td><?= nl2br(Html::encode($triagem->queixaprincipal ?? '—')) ?></td>
            </tr>
            <tr>
                <th>Descrição dos Sintomas</th>
                <td><?= nl2br(Html::encode($triagem->descricaosintomas ?? '—')) ?></td>
            </tr>
            <tr>
                <th>Início dos Sintomas</th>
                <td><?= Html::encode($triagem->iniciosintomas ?? '—') ?></td>
            </tr>
            <tr>
                <th>Discriminação Principal</th>
                <td><?= Html::encode($triagem->discriminacaoprincipal ?? '—') ?></td>
            </tr>
            <tr>
                <th>Data da Triagem</th>
                <td><?= Yii::$app->formatter->asDatetime($triagem->datatriagem, 'php:d/m/Y H:i') ?></td>
            </tr>
        </table>
    </div>
<?php endif; ?>

<!-- RODAPÉ -->
<div class="footer">
    EmergencySTS · Relatório gerado automaticamente · <?= date('d/m/Y H:i') ?>
</div>

</body>
</html>
