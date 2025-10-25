<?php
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
$this->title = 'EmergencySTS | Sistema de Triagem';
?>

<nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-success" href="<?= Yii::$app->homeUrl ?>">
            <img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" alt="Logo EmergencySTS" style="height:50px; margin-right:10px;">EmergencySTS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center mb-2 mb-lg-0">
                <li class="nav-item"><a href="index.php" class="nav-link">Início</a></li>
                <li class="nav-item"><a href="#triagem" class="nav-link">Triagem</a></li>
                <li class="nav-item"><a href="<?= Yii::$app->urlManager->createUrl(['site/about']) ?>" class="nav-link">Sobre</a></li>
                <li class="nav-item"><a href="<?= Yii::$app->urlManager->createUrl(['site/contact']) ?>" class="nav-link">Contactos</a></li>
            </ul>

            <div class="d-flex align-items-center ms-lg-3 mt-3 mt-lg-0">
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Pesquisar" aria-label="Search">
                    <button class="btn btn-outline-success btn-sm" type="submit">Search</button>
                </form>
            </div>

            <?php if (Yii::$app->user->isGuest): ?>
                <!-- Mostra o botão de login se o utilizador NÃO estiver autenticado -->
                <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="btn btn-success btn-sm ms-2">Login</a>
            <?php else: ?>
                <!-- Mostra o nome do utilizador e botão de logout se estiver autenticado -->
                <div class="dropdown ms-2">
                    <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= Html::encode(Yii::$app->user->identity->username) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'dropdown-item']) ?>
                            <?= Html::submitButton('Logout', ['class' => 'btn btn-link text-danger p-0 m-0']) ?>
                            <?= Html::endForm() ?>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

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
                        <h5 class="text-warning fw-bold">Prioridade Amarela</h5>
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
                        <p>Situação não urgente. Tempo máximo: indefinido.</p>
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

<!-- CONTACTOS -->
<section id="contactos" class="text-center py-5 bg-white">
    <div class="container">
        <h2 class="text-success mb-4">Contactos</h2>
        <p class="lead">Entre em contacto com a equipa de suporte do hospital:</p>
        <p><strong>Email:</strong> suporte@emergencysts.pt</p>
        <p><strong>Telefone:</strong> +351 900 123 456</p>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">© <?= date('Y') ?> EmergencySTS — Sistema de Triagem de Urgências</p>
</footer>
