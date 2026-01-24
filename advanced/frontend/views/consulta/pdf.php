<?php
use yii\helpers\Html;

/** @var common\models\Consulta $consulta */
/** @var common\models\Triagem|null $triagem */

$triagem = $triagem ?? $consulta->triagem ?? null;

// Paciente
$paciente = $consulta->userprofile;

// Idade
$idade = '';
if (!empty($paciente->data_nascimento)) {
    $idade = date_diff(
            date_create($paciente->data_nascimento),
            date_create('today')
    )->y;
}

// Logo
$logoPath = Yii::getAlias('@frontend/web/img/logo.png');

// Prioridade
$prio = $triagem->pulseira->prioridade ?? 'Pendente';

$bgHex = match ($prio) {
    'Vermelho' => '#dc3545', // Vermelho Bootstrap
    'Laranja'  => '#fd7e14', // Laranja
    'Amarelo'  => '#ffc107', // Amarelo
    'Verde'    => '#198754', // Verde
    'Azul'     => '#0d6efd', // Azul
    'Pendente' => '#6c757d', // Cinzento
    default    => '#6c757d',
};

$textHex = ($prio === 'Amarelo') ? '#000000' : '#ffffff';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Relatório da Consulta #<?= Html::encode($consulta->id) ?></title>

    <style>

    </style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="brand">
        <?php if (file_exists($logoPath)): ?>
            <img src="<?= $logoPath ?>" class="logo" alt="">
        <?php endif; ?>
        <div class="brand-title">EmergencySTS</div>
    </div>

    <div class="meta">
        <div style="font-size: 20px; font-weight: bold;">
            Consulta #<?= Html::encode($consulta->id) ?>
        </div>
        <div>Gerado em <?= date('d/m/Y H:i') ?></div>
    </div>
</div>

<!-- PACIENTE -->
<div class="card">
    <div class="section-title">Dados do Paciente</div>
    <table>
        <tr><th>Nome</th><td><?= Html::encode($paciente->nome ?? '—') ?></td></tr>
        <tr><th>Email</th><td><?= Html::encode($paciente->email ?? '—') ?></td></tr>
        <tr><th>Telefone</th><td><?= Html::encode($paciente->telefone ?? '—') ?></td></tr>
    </table>
</div>

<!-- CONSULTA -->
<div class="card">
    <div class="section-title">Consulta</div>
    <table>
        <tr>
            <th>Data</th>
            <td><?= Yii::$app->formatter->asDatetime($consulta->data_consulta, 'php:d/m/Y H:i') ?></td>
        </tr>
        <tr>
            <th>Estado</th>
            <td><?= Html::encode($consulta->estado ?? '—') ?></td>
        </tr>
        <tr>
            <th>Prioridade</th>
            <td>
                 <span class="badge" style="background-color: <?= $bgHex ?>; color: <?= $textHex ?>;"><?= Html::encode($prio) ?> </span>
            </td>
        </tr>
        <tr>
            <th>Motivo</th>
            <td><?= nl2br(Html::encode($triagem->motivoconsulta ?? '—')) ?></td>
        </tr>
        <tr>
            <th>Observações</th>
            <td><?= nl2br(Html::encode($consulta->observacoes ?? '—')) ?></td>
        </tr>
    </table>
</div>

<!-- TRIAGEM -->
<?php if ($triagem): ?>
    <div class="card">
        <div class="section-title">Detalhes da Triagem</div>
        <table>
            <tr>
                <th>Queixa Principal</th>
                <td><?= Html::encode($triagem->queixaprincipal ?? '—') ?></td>
            </tr>
            <tr>
                <th>Descrição</th>
                <td><?= nl2br(Html::encode($triagem->descricaosintomas ?? '—')) ?></td>
            </tr>
            <tr>
                <th>Início dos Sintomas</th>
                <td>
                    <?= $triagem->iniciosintomas
                            ? Yii::$app->formatter->asDatetime(
                                    str_replace('T', ' ', $triagem->iniciosintomas),
                                    'php:d/m/Y H:i') : '—'
                    ?>
                </td>
            </tr>
            <tr>
                <th>Data da Triagem</th>
                <td><?= Yii::$app->formatter->asDatetime($triagem->datatriagem, 'php:d/m/Y H:i') ?></td>
            </tr>
        </table>
    </div>
<?php endif; ?>

<!-- FOOTER -->
<div class="footer">
    EmergencySTS · Documento gerado automaticamente · <?= date('d/m/Y H:i') ?>
</div>

</body>
</html>
