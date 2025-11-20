<?php
use yii\helpers\Html;

$this->title = 'Painel do Enfermeiro';
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/dashboard.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/dashboard/enfermeiro.css');

?>

<div class="dashboard-container">

    <h1 class="mb-4 d-flex align-items-center text-success fw-bold">
        <i class="bi bi-clipboard2-pulse fs-2 me-2"></i>
        Painel do Enfermeiro
    </h1>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="card dashboard-card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-clipboard2-heart fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">Triagem</h5>
                        <p class="text-muted mb-0">Registar e consultar triagens</p>
                    </div>
                </div>
                <?= Html::a('Aceder', ['/triagem/index'], ['class' => 'btn btn-info mt-3 w-100 text-dark fw-semibold']) ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card dashboard-card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-danger bg-opacity-10 text-danger me-3">
                        <i class="bi bi-upc-scan fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">Pulseiras</h5>
                        <p class="text-muted mb-0">Gerir pulseiras e prioridades</p>
                    </div>
                </div>
                <?= Html::a('Aceder', ['/pulseira/index'], ['class' => 'btn btn-danger mt-3 w-100 text-white fw-semibold']) ?>
            </div>
        </div>

    </div>

</div>

<style>
    .dashboard-card { border-radius: 16px; transition: 0.2s; }
    .dashboard-card:hover { transform: translateY(-3px); }
    .icon-circle {
        width: 60px; height: 60px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
</style>
