<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Painel do Médico';
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/dashboard/medico.css');
?>

<div class="dashboard-container">

    <h1 class="mb-4 d-flex align-items-center text-success fw-bold">
        <i class="bi bi-stethoscope fs-2 me-2"></i>
        Painel do Médico
    </h1>

    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-journal-medical fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">Consultas</h5>
                        <p class="text-muted mb-0">Gerir e acompanhar consultas</p>
                    </div>
                </div>
                <?= Html::a('Aceder', ['/consulta/index'], ['class' => 'btn btn-success mt-3 w-100']) ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-prescription fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">Prescrições</h5>
                        <p class="text-muted mb-0">Emitir ou gerir prescrições</p>
                    </div>
                </div>
                <?= Html::a('Aceder', ['/prescricao/index'], ['class' => 'btn btn-primary mt-3 w-100']) ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-capsule-pill fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">Medicamentos</h5>
                        <p class="text-muted mb-0">Listar e consultar medicamentos</p>
                    </div>
                </div>
                <?= Html::a('Aceder', ['/medicamento/index'], ['class' => 'btn btn-warning mt-3 w-100 text-dark fw-semibold']) ?>
            </div>
        </div>

    </div>

</div>
