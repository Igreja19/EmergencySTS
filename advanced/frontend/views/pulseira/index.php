<?php
use yii\helpers\Html;

$this->title = 'Pulseira de Triagem - EmergencySTS';

// 🔹 Verifica se existe pulseira antes de tentar aceder
if (!$pulseira) {
    echo '<div class="container py-5 text-center">
            <div class="alert alert-warning rounded-4 shadow-sm p-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Nenhuma pulseira encontrada.
            </div>
            ' . Html::a('<i class="bi bi-arrow-left-circle me-2"></i> Voltar à Triagem', ['triagem/formulario'], [
                    'class' => 'btn btn-success mt-3 px-4 py-2'
            ]) . '
          </div>';
    return;
}

// 🔹 Define cores de acordo com a prioridade
$cores = [
        'Vermelha' => '#dc3545',
        'Laranja' => '#fd7e14',
        'Amarela' => '#ffc107',
        'Verde' => '#198754',
        'Azul' => '#0d6efd',
];
$cor = $cores[$pulseira->prioridade] ?? '#198754';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h4 class="fw-bold text-success">Pulseira de Triagem</h4>
        <p class="text-muted">Protocolo de Manchester</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4" id="pulseira-card">
        <div class="border-3 rounded-3 p-4 position-relative" id="pulseiraBox">

            <div class="border-2 p-4 rounded-4" style="border:2px solid <?= $cor ?>;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Número de Triagem</h6>
                    <span class="badge text-dark fw-semibold px-3 py-2"
                          style="background-color: <?= $cor ?>33; border: 1px solid <?= $cor ?>; color: <?= $cor ?>;">
                        <?= strtoupper(Html::encode($pulseira->prioridade)) ?>
                    </span>
                </div>
                <h2 class="fw-bold mb-4"><?= Html::encode($pulseira->codigo) ?></h2>

                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1 fw-semibold text-dark">Paciente</p>
                        <p class="text-muted"><?= Html::encode($pacienteNome ?? 'Desconhecido') ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 fw-semibold text-dark">Data</p>
                        <p class="text-muted">
                            <?= date('d/m/Y H:i', strtotime($pulseira->tempoentrada ?? date('Y-m-d H:i:s'))) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="alert mt-4 rounded-4 d-flex align-items-start"
                 style="background-color: <?= $cor ?>15; border-left: 5px solid <?= $cor ?>;">
                <i class="bi bi-info-circle-fill me-3 fs-4" style="color: <?= $cor ?>"></i>
                <div>
                    <h6 class="fw-bold mb-1" style="color: <?= $cor ?>">
                        Prioridade: <?= Html::encode($pulseira->prioridade) ?>
                    </h6>
                    <p class="mb-0 small text-muted">
                        O seu caso foi classificado como <strong><?= strtolower($pulseira->prioridade) ?></strong>.
                        Aguarde na sala de espera até ser chamado.
                    </p>
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-success" id="downloadPDF">
                    <i class="bi bi-download me-2"></i> Guardar Pulseira
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 JS para gerar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.getElementById('downloadPDF').addEventListener('click', function () {
        const element = document.getElementById('pulseiraBox');
        const opt = {
            margin: 0.5,
            filename: 'pulseira-triagem.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().from(element).set(opt).save();
    });
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
