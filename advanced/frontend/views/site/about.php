<?php

/** @var yii\web\View $this */
use yii\bootstrap5\Html;

$this->title = 'Sobre Nós';
?>
<section class="bg-success text-white text-center rounded-circle py-5 mt-5">
    <div class="container">
        <h1 class="display-5 fw-bold">Sobre o EmergencySTS</h1>
        <p class="lead mt-3 mb-0">Eficiência, Prioridade e Cuidado — o futuro do atendimento hospitalar.</p>
    </div>
</section>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <!-- Texto -->
            <div class="col-lg-6">
                <h2 class="fw-bold text-success mb-4">Quem Somos</h2>
                <p class="text-secondary mb-3">
                    O <strong>EmergencySTS</strong> é um sistema de triagem inteligente desenvolvido para otimizar o atendimento em
                    serviços de urgência. Através de algoritmos baseados no sistema de Manchester, o sistema
                    classifica a gravidade dos pacientes em tempo real, garantindo que os casos mais críticos
                    sejam atendidos primeiro.
                </p>
                <p class="text-secondary mb-3">
                    A nossa missão é <strong>melhorar a eficiência</strong> e <strong>reduzir o tempo de espera</strong> dos pacientes,
                    oferecendo uma plataforma intuitiva, segura e adaptável às necessidades dos profissionais de saúde.
                </p>
                <a href="<?= Yii::$app->urlManager->createUrl(['site/contact']) ?>" class="btn btn-success px-4 py-2 mt-3 rounded-pill shadow-sm">
                    Contactar Equipa
                </a>
            </div>

            <!-- Imagem -->
            <div class="col-lg-6 text-center">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/about-1.jpg" class="img-fluid rounded shadow" alt="Profissionais de Saúde">
            </div>
        </div>
    </div>
</section>
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold text-success mb-4">A Nossa Missão</h2>
        <p class="text-muted mb-5">
            Tornar o processo de triagem mais rápido, humano e tecnológico — salvando vidas através da inovação.
        </p>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <i class="bi bi-heart-pulse fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Cuidado e Empatia</h5>
                        <p class="text-muted">Cada decisão é guiada por valores humanos e foco no bem-estar do paciente.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <i class="bi bi-cpu fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Tecnologia Inteligente</h5>
                        <p class="text-muted">Utilizamos algoritmos modernos para avaliar a gravidade clínica em segundos.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <i class="bi bi-people fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Equipa Multidisciplinar</h5>
                        <p class="text-muted">Profissionais experientes nas áreas da saúde e tecnologia.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

