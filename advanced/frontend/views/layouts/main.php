<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Owl Carousel -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/css/style.css">

    <style>
        /* 🔹 Navbar */
        .navbar {
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            background-color: #212529 !important; /* reforço escuro ao rolar */
        }

        /* 🔹 Botões */
        .btn-success {
            background-color: #198754 !important;
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #16a34a !important;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.4);
            transform: translateY(-2px);
        }

        /* 🔹 Footer */
        footer {
            background-color: #111;
            color: #aaa;
        }

        footer a {
            color: #198754;
            text-decoration: none;
            font-weight: 500;
        }

        footer a:hover {
            color: #16a34a;
        }
    </style>

    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100 bg-light">
<?php $this->beginBody() ?>

<!-- 🔹 NAVBAR GLOBAL FIXA -->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-success" href="<?= Yii::$app->homeUrl ?>">
            <img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"
                 alt="Logo EmergencySTS" style="height:50px; margin-right:10px;">
            EmergencySTS
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/index']) ?>" class="nav-link">Início</a>
                </li>
                <li class="nav-item">
                    <a href="<?= Yii::$app->urlManager->createUrl(['triagem/index']) ?>" class="nav-link">Triagem</a>
                </li>
                <li class="nav-item">
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/about']) ?>" class="nav-link">Sobre</a>
                </li>
                <li class="nav-item">
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/contact']) ?>" class="nav-link">Contactos</a>
                </li>
            </ul>

            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>"
                   class="btn btn-success btn-sm ms-2">Login</a>
            <?php else: ?>
                <div class="dropdown ms-2">
                    <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <?= Html::encode(Yii::$app->user->identity->username) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item"
                               href="<?= Yii::$app->urlManager->createUrl(['user-profile/view',
                                       'id' => Yii::$app->user->identity->userprofile->id]) ?>">
                                <i class="bi bi-person-circle me-2"></i>Perfil
                            </a>
                        </li>
                        <li>
                            <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'dropdown-item']) ?>
                            <?= Html::submitButton('<i class="bi bi-box-arrow-right me-2"></i>Logout', [
                                    'class' => 'btn btn-link text-danger p-0 m-0'
                            ]) ?>
                            <?= Html::endForm() ?>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- 🔹 CONTEÚDO PRINCIPAL -->
<main role="main" class="flex-shrink-0">
    <div class="container-fluid px-0">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<!-- 🔹 FOOTER GLOBAL -->
<footer class="text-light py-4 border-top">
    <div class="container text-center">
        <p class="mb-1 small">
            <img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"
                 alt="Logo EmergencySTS" style="height:30px; margin-right:10px;">
            <span class="text-success fw-semibold">EmergencySTS</span> <?= date('Y') ?> —
            Todos os direitos reservados.
        </p>
        <p class="mb-0 small">
            Desenvolvido por
            <a href="<?= Yii::$app->urlManager->createUrl(['team/index']) ?>">
                EmergencySTS Dev Team
            </a>
        </p>
    </div>
</footer>

<!-- 🔹 Bootstrap e Owl Carousel -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php
$this->registerJsFile(
        'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
        ['depends' => [\yii\web\JqueryAsset::class]]
);

// JS extra: navbar sticky + carousel
$this->registerJs(<<<JS
// Navbar muda estilo ao rolar
window.addEventListener('scroll', function() {
    const nav = document.querySelector('.navbar');
    nav.classList.toggle('scrolled', window.scrollY > 20);
});

// Inicia o Owl Carousel se existir
if ($(".header-carousel").length) {
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 800,
        items: 1,
        loop: true,
        dots: true,
        nav: false
    });
    console.log("🟢 Owl Carousel iniciado");
}
JS);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
