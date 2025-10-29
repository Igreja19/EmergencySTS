<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<?php
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
$this->title = 'EmergencySTS | Sistema de Triagem';

?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-left">
        <div class="hero-content">
            <h1>Emergência Eficiente é a Base de um Atendimento Seguro</h1>
            <div class="stats">
                <div><h2>12</h2><p>Médicos em Serviço</p></div>
                <div><h2>36</h2><p>Profissionais de Saúde</p></div>
                <div><h2>240</h2><p>Pacientes Atendidos</p></div>
            </div>
        </div>
    </div>

    <div class="hero-right">
        <div class="owl-carousel header-carousel">
            <div class="item position-relative">
                <img src="<?= Yii::getAlias('@web') ?>/img/carousel-1.jpg" alt="">
                <div class="carousel-caption"><h1>Triagem</h1></div>
            </div>
            <div class="item position-relative">
                <img src="<?= Yii::getAlias('@web') ?>/img/carousel-2.jpg" alt="">
                <div class="carousel-caption"><h1>Atendimento</h1></div>
            </div>
            <div class="item position-relative">
                <img src="<?= Yii::getAlias('@web') ?>/img/carousel-3.jpg" alt="">
                <div class="carousel-caption"><h1>Suporte</h1></div>
            </div>
        </div>
    </div>
</section>

<!-- TRIAGEM -->
<section id="triagem" class="py-5 bg-white">
    <div class="container text-center">
        <h2 class="text-success mb-5">Triagem em Tempo Real</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-danger shadow-sm">
                    <div class="card-body">
                        <h5 class="text-danger fw-bold">Prioridade Vermelha</h5>
                        <p>Emergência vital — atendimento imediato.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-warning shadow-sm">
                    <div class="card-body">
                        <h5 class="laranja fw-bold">Prioridade Laranja</h5>
                        <p>Caso muito urgente. Tempo máximo: 10 minutos.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning shadow-sm">
                    <div class="card-body">
                        <h5 class="text-amarelo fw-bold">Prioridade Amarela</h5>
                        <p>Caso urgente, mas estável. Tempo máximo: 60 minutos.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success shadow-sm">
                    <div class="card-body">
                        <h5 class="text-success fw-bold">Prioridade Verde</h5>
                        <p>Situação Pouco Urgente. Tempo máximo: 120 minutos.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success shadow-sm">
                    <div class="card-body">
                        <h5 class="text-primary fw-bold">Prioridade Azul</h5>
                        <p>Situação não urgente. Tempo máximo: 240 minutos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SOBRE -->
<section id="sobre-nos" class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- IMAGENS À ESQUERDA -->
            <div class="col-lg-6 position-relative text-center">
                <div class="img-box">
                    <img src="<?= Yii::getAlias('@web') ?>/img/about-2.jpg" alt="Equipa médica" class="img-fluid rounded shadow-sm main-img">
                    <img src="<?= Yii::getAlias('@web') ?>/img/about-1.jpg" alt="Médico sorridente" class="img-fluid rounded shadow-sm sub-img">
                </div>
            </div>

            <!-- TEXTO À DIREITA -->
            <div class="col-lg-6">
                <span class="badge rounded-pill bg-light text-success border border-success px-3 py-2 mb-3">Sobre Nós</span>
                <h2 class="fw-bold text-dark mb-3">Por que confiar em nós? <br> Conheça a nossa equipa!</h2>
                <p class="text-muted mb-4">
                    O EmergencySTS é composto por profissionais dedicados à melhoria contínua dos serviços hospitalares.
                    Garantimos qualidade, rapidez e humanização no atendimento.
                </p>

                <ul class="list-unstyled mb-4">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Cuidados de saúde de qualidade</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Médicos altamente qualificados</li>
                    <li><i class="bi bi-check-circle text-success me-2"></i>Suporte e monitorização contínuos</li>
                </ul>

                <a href="<?= Yii::$app->urlManager->createUrl(['site/about']) ?>" class="btn btn-success px-4 py-2 rounded-pill">Ler mais</a>
            </div>
        </div>
    </div>
</section>
<!-- Secção: Porque Escolher-nos -->
<div class="container-fluid1" style="background-color: #198754  ;">
    <div class="container py-4">
        <div class="row g-5 align-items-center">
            <!-- Texto -->
            <div class="col-lg-6 text-white">
                <div class="mb-3">
                    <span class="badge bg-light text-success px-3 py-2 fw-semibold">Funcionalidades</span>
                </div>
                <h1 class="fw-bold mb-4">Porque Escolher-nos</h1>
                <p class="mb-4">
                    O nosso sistema de triagem hospitalar foi desenvolvido para otimizar o atendimento nas urgências,
                    garantindo rapidez, segurança e prioridade aos casos mais críticos. Com uma equipa experiente e
                    tecnologia inovadora, asseguramos uma resposta eficaz e humanizada em cada atendimento.
                </p>

                <div class="row gy-4">
                    <div class="col-6 d-flex align-items-center">
                        <div class="bg-light rounded-circle p-3 me-3 shadow-sm">
                            <i class="bi bi-person-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-light">Profissionais</h6>
                            <h5 class="fw-bold mb-0 text-white">Experientes</h5>
                        </div>
                    </div>

                    <div class="col-6 d-flex align-items-center">
                        <div class="bg-light rounded-circle p-3 me-3 shadow-sm">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-light">Serviços</h6>
                            <h5 class="fw-bold mb-0 text-white">De Qualidade</h5>
                        </div>
                    </div>

                    <div class="col-6 d-flex align-items-center">
                        <div class="bg-light rounded-circle p-3 me-3 shadow-sm">
                            <i class="bi bi-chat-dots-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-light">Atendimento</h6>
                            <h5 class="fw-bold mb-0 text-white">Personalizado</h5>
                        </div>
                    </div>

                    <div class="col-6 d-flex align-items-center">
                        <div class="bg-light rounded-circle p-3 me-3 shadow-sm">
                            <i class="bi bi-headset text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-light">Suporte</h6>
                            <h5 class="fw-bold mb-0 text-white">24 Horas</h5>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Imagem -->
            <div class="col-lg-6 text-center">
                <img src="img/feature.jpg" class="img-fluid rounded-3 shadow-lg" alt="Equipa médica em triagem hospitalar">
            </div>
        </div>
    </div>
</div>

<!-- Médicos Experientes -->
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="border border-secondary text-secondary px-3 py-1 rounded-pill fw-semibold">Médicos</span>
        <h1 class="fw-bold mt-3">Os Nossos Médicos Experientes</h1>
    </div>

    <div class="row g-4">
        <!-- Card Médico -->
        <div class="col-md-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['doutor/view', 'id' => 1]) ?>" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm doctor-card">
                    <div class="position-relative overflow-hidden">
                        <img src="img/doctor1.jpg" class="card-img-top" alt="Dr. João Silva">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold mb-1">Dr. João Silva</h5>
                        <p class="text-muted mb-0">Emergências</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 2 -->
        <div class="col-md-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['doutor/view', 'id' => 2]) ?>" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm doctor-card">
                    <div class="position-relative overflow-hidden">
                        <img src="img/doctor2.jpg" class="card-img-top" alt="Dra. Marta Costa">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold mb-1">Dra. Marta Costa</h5>
                        <p class="text-muted mb-0">Pediatria</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 3 -->
        <div class="col-md-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['doutor/view', 'id' => 3]) ?>" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm doctor-card">
                    <div class="position-relative overflow-hidden">
                        <img src="img/doctor3.jpg" class="card-img-top" alt="Dra. Inês Duarte">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold mb-1">Dra. Inês Duarte</h5>
                        <p class="text-muted mb-0">Cardiologia</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 4 -->
        <div class="col-md-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['doutor/view', 'id' => 4]) ?>" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm doctor-card hover-shadow">
                    <div class="position-relative overflow-hidden">
                        <img src="img/doctor4.jpg" class="card-img-top" alt="Dr. Ricardo Matos">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold mb-1">Dr. Ricardo Matos</h5>
                        <p class="text-muted mb-0">Neurologia</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- ====== FOOTER ====== -->
<footer class="bg-dark text-light pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row gy-4">

            <!-- Endereço -->
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3 text-success">Endereço</h5>
                <p class="mb-2"><i class="bi bi-geo-alt-fill text-success me-2"></i> 123 Rua Central, Lisboa, Portugal</p>
                <p class="mb-2"><i class="bi bi-telephone-fill text-success me-2"></i> +351 987 654 321</p>
                <p class="mb-3"><i class="bi bi-envelope-fill text-success me-2"></i> suporte@emergencysts.pt</p>

                <div class="d-flex mt-3">
                    <a href="#" class="btn btn-outline-success btn-sm rounded-circle me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-success btn-sm rounded-circle me-2"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="btn btn-outline-success btn-sm rounded-circle me-2"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="btn btn-outline-success btn-sm rounded-circle"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <!-- Serviços -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold mb-3 text-success">Serviços</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?= Yii::$app->urlManager->createUrl(['triagem/index']) ?>" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right me-2 text-success"></i>Triagem
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= Yii::$app->urlManager->createUrl(['consulta/historico']) ?>" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right me-2 text-success"></i>Histórico de Pacientes
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= Yii::$app->urlManager->createUrl(['pulseira/index']) ?>" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right me-2 text-success"></i>Tempo de Espera
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Links Rápidos -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold mb-3 text-success">Links Rápidos</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?= Yii::$app->urlManager->createUrl(['site/about']) ?>" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right me-2 text-success"></i>Sobre Nós
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= Yii::$app->urlManager->createUrl(['site/contact']) ?>" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right me-2 text-success"></i>Contactos
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= Yii::$app->urlManager->createUrl(['terms/index']) ?>" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right me-2 text-success"></i>Termos e Condições
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <!--
            <div class="col-lg-2 col-md-6">
                <h5 class="fw-bold mb-3 text-success">Newsletter</h5>
                <p class="small">Subscreve para receber as últimas novidades e atualizações.</p>
                <form class="d-flex">
                    <input type="email" class="form-control form-control-sm me-2" placeholder="O teu email">
                    <button class="btn btn-success btn-sm">OK</button>
                </form>
            </div>
        </div>
        -->

        <!-- Separador -->
        <hr class="border-secondary my-4">

        <!-- Copyright -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <small>© <span class="text-success fw-semibold">EmergencySTS</span> <?= date('Y') ?>. Todos os direitos reservados.</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small>Desenvolvido por <a href="<?= Yii::$app->urlManager->createUrl(['team/index']) ?>" class="text-success text-decoration-none fw-semibold">EmergencySTS Dev Team</a></small>
            </div>
        </div>
    </div>
</footer>



