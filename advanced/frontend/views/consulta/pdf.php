<?php
use yii\helpers\Html;

/** @var common\models\Consulta $consulta */
/** @var common\models\Triagem|null $triagem */

$triagem = $triagem ?? $consulta->triagem ?? null;

$paciente = $consulta->userprofile;

$idade = '';
if (!empty($paciente->data_nascimento)) {
    $idade = date_diff(
            date_create($paciente->data_nascimento),
            date_create('today')
    )->y;
}

$logoPath = Yii::getAlias('@frontend/web/img/logo.png');

$prio = $triagem->pulseira->prioridade ?? 'Pendente';

$prioClass = match ($prio) {
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

    </style>
</head>

<body>

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

<div class="card">
    <div class="section-title">Dados do Paciente</div>
    <table>
        <tr><th>Nome</th><td><?= Html::encode($paciente->nome ?? '—') ?></td></tr>
        <tr><th>Email</th><td><?= Html::encode($paciente->email ?? '—') ?></td></tr>
        <tr><th>Telefone</th><td><?= Html::encode($paciente->telefone ?? '—') ?></td></tr>
    </table>
</div>

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
                 <span class="badge" style="background: <?= $prioClass ?>;"><?= Html::encode($prio) ?> </span>
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

<div class="footer">
    EmergencySTS · Documento gerado automaticamente · <?= date('d/m/Y H:i') ?>
</div>

</body>
</html>
