<?php
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity ?? null;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/navbar.css');

?>

<div id="navbar-config"
     data-notif="<?= Url::to(['/notificacao/novas'], true) ?>">
</div>

<nav class="main-header navbar navbar-expand custom-navbar">

    <!-- LEFT -->
    <ul class="navbar-nav">
        <!-- Toggle sidebar -->
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <!-- Links -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::home() ?>" class="nav-link top-link">Dashboard</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/user-profile/index']) ?>" class="nav-link top-link">Utilizadores</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/triagem/index']) ?>" class="nav-link top-link">Triagem</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/pulseira/index']) ?>" class="nav-link top-link">Pulseira</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/consulta/index']) ?>" class="nav-link top-link">Consulta</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/prescricao/index']) ?>" class="nav-link top-link">Prescrições</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/medicamentos/index']) ?>" class="nav-link top-link">Medicamentos</a>
        </li>
    </ul>

    <!-- RIGHT -->
    <ul class="navbar-nav ml-auto">

        <!-- 🔔 NOTIFICAÇÕES -->
        <li class="nav-item dropdown">
            <a class="nav-link text-white" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span id="navbarNotifBadge"
                      class="badge badge-danger navbar-badge"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0">

                <span class="dropdown-header text-success fw-bold">
                    Notificações <span id="navbarNotifCount"></span>
                </span>

                <div class="dropdown-divider"></div>

                <div id="navbarNotifList">
                    <div class="text-center p-3 text-muted">
                        <i class="fas fa-spinner fa-spin"></i> A carregar...
                    </div>
                </div>

                <div class="dropdown-divider"></div>

                <a href="<?= Url::to(['/notificacao/index']) ?>"
                   class="dropdown-item dropdown-footer text-success">
                    Ver todas
                </a>
            </div>
        </li>

        <!-- USER -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle text-white" data-toggle="dropdown">
                <i class="far fa-user"></i>
                <span class="d-none d-md-inline">
                    <?= $user ? Html::encode($user->username) : "Conta" ?>
                </span>
            </a>

            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header bg-success">
                    <i class="fas fa-user-circle fa-4x"></i>
                    <p>
                        <?= $user ? Html::encode($user->username) : "Conta" ?>
                        <small>Utilizador autenticado</small>
                    </p>
                </li>

                <li class="user-footer">
                    <a href="<?= Url::to(['/user-profile/meu-perfil']) ?>"
                       class="btn btn-default btn-flat">
                        Perfil
                    </a>

                    <?= Html::a(
                            'Sair',
                            ['/site/logout'],
                            ['class' => 'btn btn-default btn-flat float-right', 'data-method' => 'post']
                    ) ?>
                </li>
            </ul>
        </li>
    </ul>
</nav>
<?php
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/layouts/navbar.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>