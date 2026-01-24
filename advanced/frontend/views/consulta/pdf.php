<?php
use yii\helpers\Html;

/** @var common\models\Consulta $consulta */
/** @var common\models\Triagem|null $triagem */
/** @var common\models\Prescricao|null $prescricao */
/** @var string $medicoNome */

$triagem = $triagem ?? $consulta->triagem ?? null;
$paciente = $consulta->userprofilePaciente ?? $consulta->userprofile;
$prescricao = $prescricao ?? ($consulta->prescricoes[0] ?? null);

$dataNasc = $paciente->datanascimento ?? $paciente->data_nascimento ?? null;
$idadeTexto = '—';

if ($dataNasc) {
    try {
        $nascimento = new DateTime($dataNasc);
        $hoje = new DateTime();
        $idadeTexto = $hoje->diff($nascimento)->y . ' anos';
    } catch (\Exception $e) {
        $idadeTexto = 'Erro na data';
    }
}

$logoPath = Yii::getAlias('@frontend/web/img/logo.png');
$logoData = "";
if (file_exists($logoPath)) {
    $logoData = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
}

$prio = $triagem->pulseira->prioridade ?? 'Pendente';
$bgHex = match ($prio) {
    'Vermelha', 'Vermelho' => '#dc3545',
    'Laranja'             => '#fd7e14',
    'Amarela', 'Amarelo'  => '#ffc107',
    'Verde'               => '#198754',
    'Azul'                => '#0d6efd',
    default               => '#6c757d',
};
$textHex = (in_array($prio, ['Amarela', 'Amarelo'])) ? '#000000' : '#ffffff';

$generoExtenso = match (strtoupper($paciente->genero ?? '')) {
    'M' => 'Masculino',
    'F' => 'Feminino',
    'O' => 'Outro', // Caso use 'O' para outros
    default => $paciente->genero ?? '—'
};
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
</head>
<body>
<div class="container">
    <table class="header-table">
        <tr>
            <td style="border:none; width: 60px;">
                <?php if ($logoData): ?>
                    <img src="<?= $logoData ?>" style="height: 50px;">
                <?php endif; ?>
            </td>
            <td style="border:none;">
                <span class="brand-title">EmergencySTS</span><br>
                <small>RELATÓRIO CLÍNICO INDIVIDUAL</small>
            </td>
            <td style="border:none; text-align: right;">
                <strong>Consulta #<?= Html::encode($consulta->id) ?></strong><br>
                Emitido em: <?= date('d/m/Y H:i') ?>
            </td>
        </tr>
    </table>

    <div class="card">
        <div class="section-title">Identificação do Paciente</div>
        <table class="vertical-table">
            <tr>
                <th style="width: 30%;">Nome Completo</th>
                <td><?= Html::encode($paciente->nome ?? '—') ?></td>
            </tr>
            <tr>
                <th>Data de Nascimento</th>
                <td><?= $dataNasc ? date('d/m/Y', strtotime($dataNasc)) : '—' ?></td>
            </tr>
            <tr>
                <th>Idade Atual</th>
                <td><strong><?= $idadeTexto ?></strong></td>
            </tr>
            <tr>
                <th>Género</th>
                <td><?= Html::encode($generoExtenso) ?></td> </tr>
            </tr>
            <tr>
                <th>Nº SNS</th>
                <td><?= Html::encode($paciente->sns ?? '—') ?></td>
            </tr>
            <tr>
                <th>NIF</th>
                <td><?= Html::encode($paciente->nif ?? '—') ?></td>
            </tr>
            <tr>
                <th>Telefone</th>
                <td><?= Html::encode($paciente->telefone ?? '—') ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= Html::encode($paciente->email ?? '—') ?></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="section-title">Avaliação de Triagem</div>
        <table>
            <tr>
                <th style="width: 30%;">Prioridade</th>
                <td>
                    <span class="badge" style="background-color: <?= $bgHex ?>; color: <?= $textHex ?>;">
                        PULSEIRA <?= strtoupper(Html::encode($prio)) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Queixa Principal</th>
                <td><?= Html::encode($triagem->queixaprincipal ?: 'Não especificada') ?></td>
            </tr>
            <tr>
                <th>Intensidade da Dor</th>
                <td><strong><?= Html::encode($triagem->intensidadedor ?? '0') ?> / 10</strong></td>
            </tr>
            <tr>
                <th>Motivo / Descrição</th>
                <td>
                    <strong>Motivo:</strong> <?= Html::encode($triagem->motivoconsulta ?: '—') ?><br>
                    <strong>Sintomas:</strong> <?= nl2br(Html::encode($triagem->descricaosintomas ?: '—')) ?>
                </td>
            </tr>
            <tr>
                <th>Alergias</th>
                <td>
                    <?= nl2br(Html::encode($triagem->alergias ?: 'Nenhuma alergia reportada.')) ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="section-title">Detalhes da Consulta</div>
        <table>
            <tr>
                <th style="width: 30%;">Médico Responsável</th>
                <td><?= Html::encode($medicoNome) ?></td>
            </tr>
            <tr>
                <th>Estado / Data</th>
                <td>
                    Estado: <?= Html::encode($consulta->estado) ?><br>
                    Início: <?= date('d/m/Y H:i', strtotime($consulta->data_consulta)) ?>
                </td>
            </tr>
            <tr>
                <th>Observações Médicas</th>
                <td><?= nl2br(Html::encode($consulta->observacoes ?: 'Sem observações.')) ?></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="section-title">Prescrição Terapêutica</div>
        <?php if ($prescricao && !empty($prescricao->prescricaomedicamentos)): ?>
            <?php foreach ($prescricao->prescricaomedicamentos as $pm): ?>
                <div class="med-item">
                    <div class="med-name"><?= Html::encode($pm->medicamento->nome) ?> - <?= Html::encode($pm->medicamento->dosagem) ?></div>
                    <div style="margin-top: 4px;"><strong>Posologia:</strong> <?= nl2br(Html::encode($pm->posologia ?: 'Conforme bula.')) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="padding: 10px;">Sem medicamentos prescritos.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        EmergencySTS - Documento Gerado em <?= date('d/m/Y H:i') ?>
    </div>
</div>
</body>
</html>