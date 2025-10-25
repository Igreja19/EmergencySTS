<?php
$this->title = 'EmergencySTS - Serviço de Urgências';
?>

<div class="container py-5">
    <!-- Secção principal -->
    <div class="card shadow-sm border-0 rounded-4 text-center p-5 mb-5">
        <h3 class="fw-bold text-success mb-2">Bem-vindo ao Serviço de Urgências</h3>
        <p class="text-muted mb-4">Sistema de Triagem - Protocolo EmergencySTS</p>

        <div class="d-flex flex-column align-items-center gap-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['triagem/formulario']) ?>" class="btn btn-success btn-lg fw-semibold px-5 py-3 shadow-sm">
                <i class="bi bi-file-earmark-text me-2"></i> Preencher Formulário Clínico
            </a>

            <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-3">
                <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="btn btn-outline-success px-4 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                </a>
                <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="btn btn-outline-success px-4 py-2 fw-semibold">
                    <i class="bi bi-person me-2"></i> Entrar como Convidado
                </a>
            </div>
        </div>
    </div>

    <!-- Cards informativos -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3">
                <i class="bi bi-clock fs-2 text-success mb-2"></i>
                <h5 class="fw-bold">Tempo de Espera</h5>
                <p class="text-muted mb-0">Consultar tempo estimado de espera</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3">
                <i class="bi bi-arrow-repeat fs-2 text-success mb-2"></i>
                <h5 class="fw-bold">Histórico</h5>
                <p class="text-muted mb-0">Ver consultas e resultados anteriores</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3 position-relative">
                <i class="bi bi-bell fs-2 text-success mb-2"></i>
                <h5 class="fw-bold">Notificações</h5>
                <p class="text-muted mb-0">Alertas e atualizações</p>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">3</span>
            </div>
        </div>
    </div>

    <!-- Sobre o Protocolo de Manchester -->
    <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color: #f8fbf8;">
        <h5 class="fw-bold text-success mb-3">Sobre o Protocolo EmergencySTS</h5>
        <p class="text-muted mb-4">
            O sistema de triagem classifica os pacientes em 5 níveis de prioridade, garantindo que casos mais urgentes sejam atendidos primeiro.
        </p>

        <div class="row g-3 text-center">
            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-danger shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-danger mb-1">Emergente</p>
                    <small class="text-muted">Imediato</small>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-warning shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-warning mb-1">Muito Urgente</p>
                    <small class="text-muted">10 min</small>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-orange shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-orange mb-1">Urgente</p>
                    <small class="text-muted">60 min</small>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-success shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-success mb-1">Pouco Urgente</p>
                    <small class="text-muted">120 min</small>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-primary shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-primary mb-1">Não Urgente</p>
                    <small class="text-muted">240 min</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
