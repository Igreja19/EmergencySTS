<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<?php
use yii\bootstrap5\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $doutores */

$this->title = 'EmergencySTS | Sistema de Triagem';
?>


<?php if (Yii::$app->session->get('firstLogin')): ?>
    <?php
    Yii::$app->session->remove('firstLogin');

    $userProfile = Yii::$app->user->identity->userprofile ?? null;
    $profileUrl = $userProfile
            ? Url::to(['user-profile/update', 'id' => $userProfile->id], true)
            : Url::to(['user-profile/create'], true);
    ?>

    <script>
        window.firstLoginProfileUrl = <?= json_encode($profileUrl) ?>;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php endif;

?>

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

<section id="sobre-nos" class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 position-relative text-center">
                <div class="img-box">
                    <img src="<?= Yii::getAlias('@web') ?>/img/about-2.jpg" alt="Equipa médica" class="img-fluid rounded shadow-sm main-img">
                    <img src="<?= Yii::getAlias('@web') ?>/img/about-1.jpg" alt="Médico sorridente" class="img-fluid rounded shadow-sm sub-img">
                </div>
            </div>

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

<div class="container-fluid1" style="background-color: #198754;">
    <div class="container py-4">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 text-white">
                <div class="mb-3">
                    <span class="badge bg-light text-success px-3 py-2 fw-semibold">Funcionalidades</span>
                </div>
                <h1 class="fw-bold mb-4">Porque Escolher-nos</h1>
                <p class="mb-4">
                    O nosso sistema de triagem hospitalar foi desenvolvido para otimizar o atendimento nas urgências,
                    garantindo rapidez, segurança e prioridade aos casos mais críticos.
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

            <div class="col-lg-6 text-center">
                <img src="<?= yii::getAlias("@web/"). 'img/feature.jpg'?>" class="img-fluid rounded-3 shadow-lg" alt="Equipa médica em triagem hospitalar">
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <p class="d-inline-block border rounded-pill py-1 px-4">Doutores</p>
                <h1>Nossos Especialistas</h1>
            </div>

            <div class="row g-4">
                <?php foreach ($doutores as $id => $doc): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="team-item position-relative rounded overflow-hidden shadow-sm h-100">
                            <div class="overflow-hidden">
                                <?= Html::img('@web/img/' . $doc['photo'], [
                                        'class' => 'img-fluid',
                                        'alt' => $doc['name'],
                                        'style' => 'width: 100%; height: 250px; object-fit: cover;'
                                ]) ?>
                            </div>
                            <div class="p-4 mt-n5">
                                <div class="bg-light text-center p-4">
                                    <h5 class="fw-bold"><?= Html::encode($doc['name']) ?></h5>
                                    <span class="text-primary"><?= Html::encode($doc['speciality']) ?></span>
                                    <hr>
                                    <p class="small text-muted mb-0"><?= Html::encode($doc['bio']) ?></p>
                                    <div class="mt-3">
                                        <small><i class="bi bi-envelope-fill me-2"></i><?= Html::encode($doc['email']) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/site/index.js?v=123', ['depends' => [\yii\web\JqueryAsset::class]]);
?>