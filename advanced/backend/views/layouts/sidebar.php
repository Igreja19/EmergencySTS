<?php
use hail812\adminlte\widgets\Menu;
use yii\helpers\Url;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/sidebar.css');

// 🔹 Obter roles do utilizador
$auth = Yii::$app->authManager;
$userId = Yii::$app->user->id ?? null;
$roles = $userId ? $auth->getRolesByUser($userId) : [];
$roleNames = array_keys($roles);

$isAdmin      = in_array('admin', $roleNames);
$isMedico     = in_array('medico', $roleNames);
$isEnfermeiro = in_array('enfermeiro', $roleNames);
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="<?= Url::to(['/site/index']) ?>" class="brand-link">
        <img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"
             alt="EmergencySTS"
             class="img brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">EmergencySTS</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <?php
            echo Menu::widget([
                    'options' => [
                            'class' => 'nav nav-pills nav-sidebar flex-column',
                            'data-widget' => 'treeview',
                            'role' => 'menu',
                            'data-accordion' => 'false'
                    ],
                    'items' => [

                        // DASHBOARD
                            [
                                    'label' => 'Dashboard',
                                    'icon' => 'tachometer-alt',
                                    'url' => ['/site/index'],
                                    'options' => ['class' => 'is-dashboard'],
                                    'visible' => !Yii::$app->user->isGuest,
                            ],

                        // UTILIZADORES (Admin)
                            [
                                    'label' => 'Utilizadores',
                                    'icon' => 'users',
                                    'url' => ['/user-profile/index'],
                                    'options' => ['class' => 'is-users'],
                                    'visible' => $isAdmin,
                            ],

                        // TRIAGEM
                            [
                                    'label' => 'Triagem',
                                    'icon' => 'stethoscope',
                                    'url' => ['/triagem/index'],
                                    'options' => ['class' => 'is-triagem'],
                                    'visible' => $isAdmin || $isEnfermeiro,
                            ],

                        // PULSEIRAS
                            [
                                    'label' => 'Pulseiras',
                                    'icon' => 'id-card',
                                    'url' => ['/pulseira/index'],
                                    'options' => ['class' => 'is-pulseira'],
                                    'visible' => $isAdmin || $isEnfermeiro,
                            ],

                        // CONSULTAS ( agora com submenu )
                            [
                                    'label' => 'Consultas',
                                    'icon' => 'notes-medical',
                                    'options' => ['class' => 'is-consulta'],
                                    'visible' => $isAdmin || $isMedico,
                                    'items' => [
                                            [
                                                    'label' => 'Todas as Consultas',
                                                    'icon' => 'angle-right',
                                                    'url' => ['/consulta/index'],
                                            ],
                                            [
                                                    'label' => 'Histórico',
                                                    'icon' => 'history text-info',
                                                    'url' => ['/consulta/historico'],
                                            ],
                                    ],
                            ],

                        // PRESCRIÇÕES
                            [
                                    'label' => 'Prescrições',
                                    'icon' => 'prescription-bottle-alt',
                                    'url' => ['/prescricao/index'],
                                    'options' => ['class' => 'is-prescricao'],
                                    'visible' => $isAdmin || $isMedico,
                            ],

                        // MEDICAMENTOS
                            [
                                    'label' => 'Medicamentos',
                                    'icon' => 'capsules',
                                    'url' => ['/medicamento/index'],
                                    'options' => ['class' => 'is-medicamento'],
                                    'visible' => $isAdmin || $isMedico,
                            ],

                        // NOTIFICAÇÕES
                            [
                                    'label' => 'Notificações',
                                    'icon' => 'bell',
                                    'url' => ['/notificacao/index'],
                                    'options' => ['class' => 'is-notificacao'],
                                    'visible' => $isAdmin || $isMedico || $isEnfermeiro,
                            ],

                        // PERFIL
                            [
                                    'label' => 'Perfil',
                                    'icon'  => 'user-cog',
                                    'url'   => ['/user-profile/meu-perfil'],
                                    'options' => ['class' => 'is-perfil'],
                                    'visible' => !Yii::$app->user->isGuest,
                            ],

                        // LOGOUT
                            [
                                    'label' => 'Sair',
                                    'icon' => 'sign-out-alt',
                                    'url' => ['/site/logout'],
                                    'visible' => !Yii::$app->user->isGuest,
                                    'template' => '
                            <a href="{url}" data-method="post" class="nav-link logout-link d-flex align-items-center">
                                <i class="nav-icon fas fa-sign-out-alt me-2"></i>
                                <span class="logout-text">Sair</span>
                            </a>',
                                    'options' => ['class' => 'is-sair'],
                            ],
                    ],
            ]);
            ?>
        </nav>
    </div>

</aside>
