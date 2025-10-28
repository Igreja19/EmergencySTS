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

    <!-- Ícones Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/css/style.css">

    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100 bg-light">
<?php $this->beginBody() ?>

<?php
// Só esconde navbar e footer na página inicial do site
$isHomePage = (Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'index');
?>

<?php if (!$isHomePage): ?>
    <header>
        <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 fixed-top shadow-sm">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center fw-bold text-success" href="<?= Yii::$app->homeUrl ?>">
                    <img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" alt="Logo EmergencySTS" style="height:50px; margin-right:10px;">EmergencySTS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Links -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center mb-2 mb-lg-0">
                        <li class="nav-item"><a href="<?= Yii::$app->urlManager->createUrl(['site/index']) ?>" class="nav-link">Início</a></li>
                        <li class="nav-item"><a href="<?= Yii::$app->urlManager->createUrl(['triagem/index']) ?>" class="nav-link">Triagem</a></li>
                        <li class="nav-item"><a href="<?= Yii::$app->urlManager->createUrl(['site/about']) ?>" class="nav-link">Sobre</a></li>
                        <li class="nav-item"><a href="<?= Yii::$app->urlManager->createUrl(['site/contact']) ?>" class="nav-link">Contactos</a></li>
                    </ul>

                    <!-- Login / Utilizador -->
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="btn btn-success btn-sm ms-2">Login</a>
                    <?php else: ?>
                        <div class="dropdown ms-2">
                            <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= Html::encode(Yii::$app->user->identity->username) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?= Yii::$app->urlManager->createUrl(['paciente/view', 'id' => Yii::$app->user->id]) ?>">
                                        <i class="bi bi-person-circle me-2"></i> Perfil
                                    </a>
                                </li>
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
    </header>
<?php endif; ?>

<main role="main" class="flex-shrink-0">
    <?php
    $controller = Yii::$app->controller->id;
    $action = Yii::$app->controller->action->id;
    $isAuthPage = ($controller === 'site' && in_array($action, ['login', 'signup', 'request-password-reset']));
    ?>

    <?php if ($isHomePage || $isAuthPage): ?>
        <!-- Páginas full-screen (sem container Bootstrap) -->
        <?= $content ?>
    <?php else: ?>
        <!-- Restantes páginas com container -->
        <div class="container mt-5 pt-4">
            <?= Breadcrumbs::widget([
                    'links' => $this->params['breadcrumbs'] ?? [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    <?php endif; ?>
</main>

<?php if (!$isHomePage): ?>
    <footer class="bg-dark text-light py-3 mt-5 border-top">
        <div class="container text-center">
            <p class="mb-1 small">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" alt="Logo EmergencySTS" style="height:30px; margin-right:10px;">
                <span class="text-success fw-semibold">EmergencySTS</span> <?= date('Y') ?>.
                Todos os direitos reservados.
            </p>
            <p class="mb-0 small">
                Desenvolvido por
                <a href="<?= Yii::$app->urlManager->createUrl(['team/index']) ?>"
                   class="text-success text-decoration-none fw-semibold">
                    EmergencySTS Dev Team
                </a>
            </p>
        </div>
    </footer>
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Carregar Owl Carousel com dependência do jQuery do Yii
$this->registerJsFile(
        'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
        ['depends' => [\yii\web\JqueryAsset::class]]
);

// Inicializar o Owl Carousel
$this->registerJs(<<<JS
console.log("🟢 Owl Carousel iniciado");
$(".header-carousel").owlCarousel({
  autoplay: true,
  smartSpeed: 800,
  items: 1,
  loop: true,
  dots: true,
  nav: false
});
JS);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
