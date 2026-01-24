<?php
/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;

AppAsset::register($this);
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layout/main.css');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" type="image/png" href="<?= Yii::$app->request->baseUrl ?>/img/logo.png">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Owl Carousel -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100 bg-light">
<?php $this->beginBody() ?>

<!-- 🔹 NAVBAR GLOBAL FIXA -->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-success" href="<?= Yii::$app->homeUrl ?>">
            <img class="img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"
                 alt="Logo EmergencySTS">
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
                <?php
                $userProfile = Yii::$app->user->identity->userprofile ?? null;
                $profileUrl = $userProfile
                        ? Yii::$app->urlManager->createUrl(['user-profile/view', 'id' => $userProfile->id])
                        : Yii::$app->urlManager->createUrl(['user-profile/create']);
                ?>
                <div class="dropdown ms-2">
                    <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <?= Html::encode(Yii::$app->user->identity->username) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= $profileUrl ?>">
                                <i class="bi bi-person-circle me-2"></i> Perfil
                            </a>
                        </li>

                        <li>
                            <?= Html::beginForm(['/site/logout'], 'post') ?>
                            <?= Html::submitButton(
                                    '<i class="bi bi-box-arrow-right me-2"></i> Logout',
                                    [
                                            'class' => 'dropdown-item text-danger w-100 text-start',
                                            'style' => 'background:none;border:0;',
                                            'encode' => false
                                    ]
                            ) ?>
                            <?= Html::endForm() ?>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main role="main" class="flex-fill">
    <div class="container-fluid px-0">
        <?= $content ?>
    </div>
</main>

<footer class="text-light py-4 border-top">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3 text-success">Endereço</h5>
                <p class="mb-2"><i class="bi bi-geo-alt-fill text-success me-2"></i> Rua Central da Saúde, 2450-100 Leiria, Portugal</p>
                <p class="mb-2"><i class="bi bi-telephone-fill text-success me-2"></i> +351 987 654 321</p>
                <p class="mb-3"><i class="bi bi-envelope-fill text-success me-2"></i> suporte@emergencysts.pt</p>
                <div class="d-flex mt-3">
                    <a href="#" class="btn btn-outline-success btn-sm rounded-circle me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-success btn-sm rounded-circle me-2"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="btn btn-outline-success btn-sm rounded-circle me-2"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="btn btn-outline-success btn-sm rounded-circle"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold mb-3 text-success">Serviços</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['triagem/index']) ?>" class="text-light text-decoration-none"><i class="bi bi-chevron-right me-2 text-success"></i>Triagem</a></li>
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['consulta/historico']) ?>" class="text-light text-decoration-none"><i class="bi bi-chevron-right me-2 text-success"></i>Histórico de Pacientes</a></li>
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['pulseira/index']) ?>" class="text-light text-decoration-none"><i class="bi bi-chevron-right me-2 text-success"></i>Tempo de Espera</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold mb-3 text-success">Links Rápidos</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['site/about']) ?>" class="text-light text-decoration-none"><i class="bi bi-chevron-right me-2 text-success"></i>Sobre Nós</a></li>
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['site/contact']) ?>" class="text-light text-decoration-none"><i class="bi bi-chevron-right me-2 text-success"></i>Contactos</a></li>
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['terms/index']) ?>" class="text-light text-decoration-none"><i class="bi bi-chevron-right me-2 text-success"></i>Termos e Condições</a></li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="row">
            <div class="col-md-6 text-center text-md-end">
                <small>Desenvolvido por <a href="<?= Url::to(['/team/index']) ?>" class="text-success text-decoration-none fw-semibold">EmergencySTS Dev Team</a></small>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
$this->registerJsFile(
        'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
        ['depends' => [\yii\web\JqueryAsset::class]]
);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/layouts/main.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
