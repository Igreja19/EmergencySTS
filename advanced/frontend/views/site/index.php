<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<?php
use yii\bootstrap5\Html;

/** @var yii\web\View $this */

$this->title = 'EmergencySTS | Sistema de Triagem';
?>

<?php if (Yii::$app->session->get('firstLogin')): ?>
    <?php
    // ⚙️ Só mostra o alerta uma vez
    Yii::$app->session->remove('firstLogin');

    // ✅ Verifica se o utilizador tem um perfil associado
    $userProfile = Yii::$app->user->identity->userprofile ?? null;

    // Se existir perfil → vai para view, se não → para create
    $profileUrl = $userProfile
            ? \yii\helpers\Url::to(['user-profile/view', 'id' => $userProfile->id])
            : \yii\helpers\Url::to(['user-profile/create']);
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: 'Bem-vindo!',
                text: 'Antes de iniciar, por favor preencha o seu perfil para continuar.',
                icon: 'info',
                confirmButtonText: 'Ok, preencher agora',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= $profileUrl ?>";
                }
            });
        });
    </script>
<?php endif; ?>

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

<!-- PORQUE ESCOLHER-NOS -->
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
                <img src="img/feature.jpg" class="img-fluid rounded-3 shadow-lg" alt="Equipa médica em triagem hospitalar">
            </div>
        </div>
    </div>
</div>

<!-- MÉDICOS -->
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="border border-secondary text-secondary px-3 py-1 rounded-pill fw-semibold">Médicos</span>
        <h1 class="fw-bold mt-3">Os Nossos Médicos Experientes</h1>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['doutor/view', 'id' => 1]) ?>" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm doctor-card">
                    <div class="position-relative overflow-hidden">
                        <img src="img/doctor2.jpg" class="card-img-top" alt="Dr. João Silva">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold mb-1">Dr. João Silva</h5>
                        <p class="text-muted mb-0">Emergências</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['doutor/view', 'id' => 2]) ?>" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm doctor-card">
                    <div class="position-relative overflow-hidden">
                        <img src="img/doctor1.jpg" class="card-img-top" alt="Dra. Marta Costa">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold mb-1">Dra. Marta Costa</h5>
                        <p class="text-muted mb-0">Pediatria</p>
                    </div>
                </div>
            </a>
        </div>

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

        <div class="col-md-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['doutor/view', 'id' => 4]) ?>" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm doctor-card">
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


