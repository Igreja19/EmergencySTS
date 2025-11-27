<?php
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity ?? null;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/navbar.css');
?>

<nav class="main-header navbar navbar-expand custom-navbar">

    <!-- LEFT -->
    <ul class="navbar-nav">
        <!-- Sidebar toggle -->
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::home() ?>" class="nav-link top-link">Dashboard</a>
        </li>

        <?php if ($isAdmin): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/user-profile/index']) ?>" class="nav-link top-link">Utilizadores</a>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin || $isEnfermeiro): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/triagem/index']) ?>" class="nav-link top-link">Triagem</a>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin || $isEnfermeiro): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/pulseira/index']) ?>" class="nav-link top-link">Pulseira</a>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin || $isMedico): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/consulta/index']) ?>" class="nav-link top-link">Consultas</a>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin || $isMedico): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/prescricao/index']) ?>" class="nav-link top-link">Prescrições</a>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin || $isMedico): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/medicamento/index']) ?>" class="nav-link top-link">Medicamentos</a>
            </li>
        <?php endif; ?>
    </ul>

    <!-- RIGHT -->
    <ul class="navbar-nav ms-auto">

        <!-- 🔔 NOTIFICAÇÕES SSE -->
        <?php if ($isAdmin || $isMedico || $isEnfermeiro): ?>
            <li class="nav-item dropdown">

                <!-- Botão da campainha -->
                <a class="nav-link text-white notif-btn" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="far fa-bell"></i>
                    <!-- .notif-badge inserido via JS -->
                </a>

                <!-- Dropdown -->
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 shadow-lg" style="width:380px;">

                    <!-- Cabeçalho -->
                    <div class="notif-header d-flex justify-content-between align-items-center p-2 bg-light border-bottom">
                        <span class="fw-semibold">Notificações</span>
                        <!-- badge verde inserida via JS -->
                    </div>

                    <!-- Corpo -->
                    <div class="notif-body" style="max-height:260px; overflow-y:auto;">
                        <div class="text-center p-3 text-muted">
                            <i class="fas fa-spinner fa-spin"></i> A carregar...
                        </div>
                    </div>

                    <div class="dropdown-divider m-0"></div>

                    <!-- Rodapé -->
                    <a href="<?= Url::to(['/notificacao/index']) ?>"
                       class="dropdown-item dropdown-footer text-success">
                        Ver todas
                    </a>

                </div>

            </li>
        <?php endif; ?>


        <!-- USER MENU -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown">
                <i class="far fa-user"></i>
                <span class="d-none d-md-inline">
                    <?= $user ? Html::encode($user->username) : "Conta" ?>
                </span>
            </a>

            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <li class="user-header bg-success text-center">
                    <i class="fas fa-user-circle fa-4x mb-2"></i>
                    <p>
                        <?= $user ? Html::encode($user->username) : "Conta" ?><br>
                        <small>Utilizador autenticado</small>
                    </p>
                </li>

                <li class="user-footer d-flex justify-content-between">
                    <a href="<?= Url::to(['/user-profile/meu-perfil']) ?>"
                       class="btn btn-default btn-flat">
                        Perfil
                    </a>

                    <?= Html::a('Sair',
                            ['/site/logout'],
                            ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']
                    ) ?>
                </li>
            </ul>
        </li>
    </ul>
</nav>
