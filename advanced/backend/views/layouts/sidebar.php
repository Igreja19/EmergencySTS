<?php
use hail812\adminlte\widgets\Menu;
use yii\helpers\Url;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/sidebar.css');

$auth = Yii::$app->authManager;
$userId = Yii::$app->user->id ?? null;
$roles = $userId ? $auth->getRolesByUser($userId) : [];
$roleNames = array_keys($roles);

$isAdmin      = in_array('admin', $roleNames);
$isMedico     = in_array('medico', $roleNames);
$isEnfermeiro = in_array('enfermeiro', $roleNames);

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/sidebar.css');
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="<?= Url::to(['/site/index']) ?>" class="brand-link modern-brand border-0">
        <div class="brand-logo-container">
            <img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"
                 alt="EmergencySTS"
                 class="brand-image-modern">
        </div>
        <span class="brand-text-modern">EmergencySTS</span>
    </a>

    <div class="sidebar">
        <nav class="mt-3">
            <?php
            echo Menu::widget([
                    'options' => [
                            'class' => 'nav nav-pills nav-sidebar flex-column nav-child-indent', // Adicionei nav-child-indent
                            'data-widget' => 'treeview',
                            'role' => 'menu',
                            'data-accordion' => 'false'
                    ],
                    'items' => [
                            [
                                    'label' => 'Dashboard',
                                    'icon' => 'tachometer-alt',
                                    'url' => ['/site/index'],
                                    'options' => ['class' => 'is-dashboard'],
                            ],
                            [
                                    'label' => 'Utilizadores',
                                    'icon' => 'users',
                                    'url' => ['/user-profile/index'],
                                    'options' => ['class' => 'is-users'],
                                    'visible' => $isAdmin,
                            ],
                            [
                                    'label' => 'Pulseiras',
                                    'icon' => 'id-card',
                                    'url' => ['/pulseira/index'],
                                    'options' => ['class' => 'is-pulseira'],
                                    'visible' => $isAdmin || $isEnfermeiro,
                            ],
                            [
                                    'label' => 'Triagem',
                                    'icon' => 'stethoscope',
                                    'url' => ['/triagem/index'],
                                    'options' => ['class' => 'is-triagem'],
                                    'visible' => $isAdmin || $isEnfermeiro,
                            ],
                            [
                                    'label' => 'Consultas',
                                    'icon' => 'notes-medical',
                                    'options' => ['class' => 'is-consulta'],
                                    'visible' => $isAdmin || $isMedico,
                                    'items' => [
                                            ['label' => 'Todas as Consultas', 'icon' => 'circle', 'url' => ['/consulta/index']],
                                            ['label' => 'Histórico', 'icon' => 'history', 'url' => ['/consulta/historico']],
                                    ],
                            ],
                            [
                                    'label' => 'Prescrições',
                                    'icon' => 'prescription-bottle-alt',
                                    'url' => ['/prescricao/index'],
                                    'options' => ['class' => 'is-prescricao'],
                                    'visible' => $isAdmin || $isMedico,
                            ],
                            [
                                    'label' => 'Medicamentos',
                                    'icon' => 'capsules',
                                    'url' => ['/medicamento/index'],
                                    'options' => ['class' => 'is-medicamento'],
                                    'visible' => $isAdmin || $isMedico,
                            ],
                            [
                                    'label' => 'Notificações',
                                    'icon' => 'bell',
                                    'url' => ['/notificacao/index'],
                                    'options' => ['class' => 'is-notificacao'],
                            ],
                            [
                                    'label' => 'Meu Perfil',
                                    'icon'  => 'user-circle',
                                    'url'   => ['/user-profile/meu-perfil'],
                                    'options' => ['class' => 'is-perfil'],
                            ],
                            [
                                    'label' => 'Sair',
                                    'icon' => 'sign-out-alt',
                                    'url' => ['/site/logout'],
                                    'template' => '<a href="{url}" data-method="post" class="nav-link logout-link mt-2">
                                        <i class="nav-icon fas fa-sign-out-alt"></i>
                                        <p>{label}</p>
                                       </a>',
                                    'options' => ['class' => 'is-sair'],
                            ],
                    ],
            ]);
            ?>
        </nav>
    </div>
</aside>
