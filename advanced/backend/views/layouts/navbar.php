<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var array|string $assetDir (opcional, não usado aqui) */

$user = Yii::$app->user->identity ?? null;
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <!-- Toggle sidebar -->
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <!-- Dashboard -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::home() ?>" class="nav-link">Dashboard</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/user-profile/index']) ?>"class="nav-link">Utilizadores
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/triagem/index']) ?>"class="nav-link">Triagem
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/pulseira/index']) ?>"class="nav-link">Pulseira
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- User dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-user"></i>
                <?= $user ? Html::encode($user->username) : 'Conta' ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?= Url::to(['/user-profile/meu-perfil']) ?>" class="dropdown-item">
                    <i class="fas fa-id-badge mr-2"></i> Perfil
                </a>
                <div class="dropdown-divider"></div>
                <?= Html::a('<i class="fas fa-sign-out-alt mr-2"></i> Terminar sessão', ['/site/logout'], [
                        'class' => 'dropdown-item',
                        'data-method' => 'post'
                ]) ?>
            </div>
        </li>
    </ul>
</nav>
