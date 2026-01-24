<?php
$this->title = 'EmergencySTS Dev Team';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge bg-light text-success px-3 py-2 fw-semibold">Equipa</span>
        <h1 class="fw-bold mt-3 text-success">EmergencySTS Dev Team</h1>
        <p class="text-muted">
            A equipa responsável pelo desenvolvimento do sistema de triagem hospitalar <strong>EmergencySTS</strong>.
        </p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center team-card overflow-hidden">
                <img src="<?= yii::getAlias("@web/"). 'img/Miguel.jpg'?>" class="card-img-top" alt="Miguel Tobias">
                <div class="card-body">
                    <h5 class="fw-bold text-success mb-1">Miguel Tobias</h5>
                    <p class="text-muted mb-1">Front-end • Back-end • Full-Stack Developer</p>
                    <p class="text-muted small mb-4">
                        Responsável pelo design, integração da lógica do sistema e manutenção das principais funcionalidades da aplicação.
                    </p>
                </div>
                <div class="social-footer bg-success py-2">
                    <a href="#" class="text-white px-3"><i class="bi bi-github fs-5"></i></a>
                    <a href="#" class="text-white px-3"><i class="bi bi-linkedin fs-5"></i></a>
                    <a href="#" class="text-white px-3"><i class="bi bi-envelope-fill fs-5"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center team-card overflow-hidden">
                <img src="<?= yii::getAlias("@web/"). ''?>" class="card-img-top" alt="Fábio Almeida">
                <div class="card-body">
                    <h5 class="fw-bold text-success mb-1">Fábio Almeida</h5>
                    <p class="text-muted mb-1">Front-end • Back-end • Full-Stack Developer</p>
                    <p class="text-muted small mb-4">
                        Foca-se na arquitetura do sistema e experiência do utilizador, garantindo estabilidade e desempenho em todas as plataformas.
                    </p>
                </div>
                <div class="social-footer bg-success py-2">
                    <a href="#" class="text-white px-3"><i class="bi bi-github fs-5"></i></a>
                    <a href="#" class="text-white px-3"><i class="bi bi-linkedin fs-5"></i></a>
                    <a href="#" class="text-white px-3"><i class="bi bi-envelope-fill fs-5"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center team-card overflow-hidden">
                <img src="img/dev3.jpg" class="card-img-top" alt="Enrique Salgado">
                <div class="card-body">
                    <h5 class="fw-bold text-success mb-1">Enrique Salgado</h5>
                    <p class="text-muted mb-1">Front-end • Back-end • Full-Stack Developer</p>
                    <p class="text-muted small mb-4">
                        Trabalha na implementação das funcionalidades e integração entre camadas da aplicação para garantir um sistema robusto e seguro.
                    </p>
                </div>
                <div class="social-footer bg-success py-2">
                    <a href="#" class="text-white px-3"><i class="bi bi-github fs-5"></i></a>
                    <a href="#" class="text-white px-3"><i class="bi bi-linkedin fs-5"></i></a>
                    <a href="#" class="text-white px-3"><i class="bi bi-envelope-fill fs-5"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">