<?php
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity ?? null;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/adminlte-custom.css');
?>

<nav class="main-header navbar navbar-expand custom-navbar">

    <!-- LEFT -->
    <ul class="navbar-nav">
        <!-- Toggle sidebar -->
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <!-- DASHBOARD (todos) -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::home() ?>" class="nav-link top-link">Dashboard</a>
        </li>

        <!-- UTILIZADORES (admin) -->
        <?php if ($isAdmin): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/user-profile/index']) ?>" class="nav-link top-link">Utilizadores</a>
            </li>
        <?php endif; ?>

        <!-- TRIAGEM (admin + enfermeiro) -->
        <?php if ($isAdmin || $isEnfermeiro): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/triagem/index']) ?>" class="nav-link top-link">Triagem</a>
            </li>
        <?php endif; ?>

        <!-- PULSEIRAS (admin + enfermeiro) -->
        <?php if ($isAdmin || $isEnfermeiro): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/pulseira/index']) ?>" class="nav-link top-link">Pulseira</a>
            </li>
        <?php endif; ?>

        <!-- CONSULTAS (admin + médico) -->
        <?php if ($isAdmin || $isMedico): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/consulta/index']) ?>" class="nav-link top-link">Consultas</a>
            </li>
        <?php endif; ?>

        <!-- PRESCRIÇÕES (admin + médico) -->
        <?php if ($isAdmin || $isMedico): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/prescricao/index']) ?>" class="nav-link top-link">Prescrições</a>
            </li>
        <?php endif; ?>

        <!-- MEDICAMENTOS (admin + médico) -->
        <?php if ($isAdmin || $isMedico): ?>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= Url::to(['/medicamento/index']) ?>" class="nav-link top-link">Medicamentos</a>
            </li>
        <?php endif; ?>
    </ul>

    <!-- RIGHT -->
    <ul class="navbar-nav ml-auto">

        <!-- NOTIFICAÇÕES (todos os staff) -->
        <?php if ($isAdmin || $isMedico || $isEnfermeiro): ?>
            <li class="nav-item dropdown">
                <a class="nav-link text-white" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span id="navbarNotifBadge"
                          class="badge badge-danger navbar-badge"
                          style="display:none;"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0">
                    <span class="dropdown-header text-success fw-bold">
                        Notificações <span id="navbarNotifCount"></span>
                    </span>

                    <div class="dropdown-divider"></div>

                    <div id="navbarNotifList" style="max-height:260px; overflow-y:auto;">
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
        <?php endif; ?>

        <!-- USER MENU (todos autenticados) -->
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

<!-- 🔔 NOTIFICAÇÕES usando AJAX (mantido do teu original) -->
<script>
    async function carregarNotificacoesNavbarReal() {
        try {
            const response = await fetch("<?= Url::to(['/notificacao/novas']) ?>");
            const data = await response.json();

            const badge = document.getElementById('navbarNotifBadge');
            const count = document.getElementById('navbarNotifCount');
            const list  = document.getElementById('navbarNotifList');

            list.innerHTML = '';

            if (!data || data.length === 0) {
                badge.style.display = 'none';
                count.textContent = '';

                list.innerHTML = `
                <div class="text-center p-3 text-muted">
                    <i class="far fa-bell-slash fa-2x"></i>
                    <p class="mt-2">Sem novas notificações</p>
                </div>
                `;
                return;
            }

            badge.style.display = 'inline-block';
            badge.textContent = data.length;

            count.textContent = data.length + " novas";

            data.forEach(n => {
                list.innerHTML += `
                <a href="#" class="dropdown-item">
                    <i class="fas fa-exclamation-circle mr-2 text-success"></i>
                    <strong>${n.ttitulo}</strong>
                    <div class="small text-muted">${n.mensagem}</div>
                    <div class="small"><i class="far fa-clock"></i> ${n.dataenvio}</div>
                </a>
                <div class="dropdown-divider"></div>
                `;
            });

        } catch (e) {
            console.error("Erro AJAX notificações:", e);
        }
    }

    carregarNotificacoesNavbarReal();
    setInterval(carregarNotificacoesNavbarReal, 10000);
</script>
