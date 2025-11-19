<?php
use yii\helpers\Html;

/** @var common\models\Consulta $consulta */
/** @var common\models\Triagem|null $triagem */

$triagem = $triagem ?? $consulta->triagem ?? null;

// Caminho do logo (mPDF aceita caminho absoluto do filesystem ou URL pública)
$logoPath = \Yii::getAlias('@frontend/web/img/logo.png');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/consulta/relatorio.css');

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
        .badge-prio {
            background: <?= $prioColor ?>;
            color:#fff;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="brand">
        <?php if (is_file($logoPath)): ?>
            <img src="<?= $logoPath ?>" class="brand-logo" alt="Logo">
        <?php endif; ?>
        <div class="brand-title">EmergencySTS</div>
    </div>
    <div class="meta">
        <div class="title">Relatório da Consulta #<?= Html::encode($consulta->id) ?></div>
        <div class="small">Gerado em <?= date('d/m/Y H:i') ?></div>
    </div>
</div>

<!-- CAPA/RESUMO -->
<div class="card">
    <div class="row">
        <div class="col">
            <div class="k">Paciente</div>
            <div class="v"><?= Html::encode($consulta->paciente->nomecompleto ?? ('ID '.$consulta->paciente_id)) ?></div>
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
            <th>Motivo da Consulta</th>
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
                <th>Queixa Principal</th>
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
