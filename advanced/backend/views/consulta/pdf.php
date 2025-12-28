<?php
use yii\helpers\Html;

/** @var common\models\Consulta $consulta */
/** @var common\models\Prescricao $prescricao */
/** @var string $medicoNome */

// Paciente (userprofile)
$paciente = $consulta->userprofilePaciente ?? $consulta->userprofile;

// Idade
$idade = '';
if (!empty($paciente->data_nascimento)) {
    $idade = date_diff(date_create($paciente->data_nascimento), date_create('today'))->y;
}

// Logo
$logoPath = Yii::getAlias('@backend/web/img/logo.png');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Prescrição #<?= Html::encode($prescricao->id) ?></title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }

        .header {
            background: #1f9d55;
            padding: 14px;
            border-radius: 6px;
            color: white;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .logo { height: 48px; margin-right: 12px; }
        .brand { display: flex; align-items: center; }
        .brand-title { font-size: 22px; font-weight: bold; }

        .meta { text-align: right; }

        .card {
            border: 1px solid #ddd;
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f9d55;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th {
            width: 28%;
            background: #f1f1f1;
            padding: 6px;
            text-align: left;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #eee;
        }

        .med-card {
            border: 1px solid #1f9d55;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="brand">
        <?php if (file_exists($logoPath)): ?>
            <img src="<?= $logoPath ?>" class="logo">
        <?php endif; ?>
        <div class="brand-title">EmergencySTS</div>
    </div>

    <div class="meta">
        <div style="font-size: 20px; font-weight: bold;">Prescrição #<?= $prescricao->id ?></div>
        <div>Gerado em <?= date('d/m/Y H:i') ?></div>
    </div>
</div>


<!-- PACIENTE -->
<div class="card">
    <div class="section-title">Dados do Paciente</div>

    <table>
        <tr><th>Nome</th><td><?= Html::encode($paciente->nome ?? '—') ?></td></tr>
        <tr>
            <th>Data de Nascimento</th>
            <td>
                <?= Html::encode($paciente->data_nascimento ?? '—') ?>
                <?php if ($idade): ?> (<?= $idade ?> anos) <?php endif; ?>
            </td>
        </tr>
        <tr><th>Género</th><td><?= Html::encode($paciente->genero ?? '—') ?></td></tr>
        <tr><th>Email</th><td><?= Html::encode($paciente->email ?? '—') ?></td></tr>
        <tr><th>Telefone</th><td><?= Html::encode($paciente->telefone ?? '—') ?></td></tr>
        <tr><th>NIF</th><td><?= Html::encode($paciente->nif ?? '—') ?></td></tr>
        <tr><th>SNS</th><td><?= Html::encode($paciente->sns ?? '—') ?></td></tr>
    </table>
</div>


<!-- MÉDICO -->
<div class="card">
    <div class="section-title">Médico Responsável</div>

    <table>
        <tr><th>Nome</th><td><?= Html::encode($medicoNome) ?></td></tr>
        <tr><th>Função</th><td><?= Html::encode($consulta->userprofile->role ?? 'Profissional de Saúde') ?></td></tr>
    </table>
</div>


<!-- CONSULTA -->
<div class="card">
    <div class="section-title">Consulta Associada</div>

    <table>
        <tr>
            <th>Data</th>
            <td><?= Yii::$app->formatter->asDatetime($consulta->data_consulta ?? $consulta->data, 'php:d/m/Y H:i') ?></td>
        </tr>
        <tr><th>Estado</th><td><?= Html::encode($consulta->estado ?? '—') ?></td></tr>
        <tr><th>Motivo</th><td><?= nl2br(Html::encode($consulta->motivoconsulta ?? '—')) ?></td></tr>
        <tr><th>Observações</th><td><?= nl2br(Html::encode($consulta->observacoes ?? '—')) ?></td></tr>
    </table>
</div>


<!-- PRESCRIÇÃO -->
<div class="card">
    <div class="section-title">Dados da Prescrição</div>

    <table>
        <tr>
            <th>Data da Prescrição</th>
            <td><?= Yii::$app->formatter->asDatetime($prescricao->dataprescricao, 'php:d/m/Y H:i') ?></td>
        </tr>
        <tr>
            <th>Observações Gerais</th>
            <td><?= nl2br(Html::encode($model->observacoes ?? '—')) ?></td>
        </tr>
    </table>
</div>


<!-- MEDICAMENTOS -->
<div class="card">
    <div class="section-title">Medicamentos Prescritos</div>

    <?php foreach ($prescricao->prescricaomedicamentos as $pm): ?>
        <div class="med-card">
            <strong style="color:#1f9d55; font-size:14px;">
                <?= Html::encode($pm->medicamento->nome) ?>
                (<?= Html::encode($pm->medicamento->dosagem) ?>)
            </strong>
            <br><br>

            <strong>Posologia:</strong><br>
            <?= nl2br(Html::encode($pm->posologia ?? '—')) ?>
        </div>
    <?php endforeach; ?>

</div>


<!-- FOOTER -->
<div class="footer">
    EmergencySTS · Documento gerado automaticamente · <?= date('d/m/Y H:i') ?>
</div>

</body>
</html>
