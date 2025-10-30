<?php
use hail812\adminlte\widgets\Menu;
use yii\helpers\Url;
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand -->
    <a href="<?= Url::to(['/site/index']) ?>" class="brand-link">
        <img src="/img/logo.png" alt="EmergencySTS" class="brand-image img-circle elevation-3" style="opacity:.9">
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
                            [
                                    'label' => 'Dashboard',
                                    'icon' => 'tachometer-alt',
                                    'url' => ['/site/index']
                            ],
                            [
                                    'label' => 'Utilizadores',
                                    'icon' => 'users',
                                    'url' => ['/user-profile/index']
                            ],
                            [
                                    'label' => 'Triagem',
                                    'icon' => 'stethoscope',
                                    'url' => ['/triagem/index']
                            ],
                            [
                                    'label' => 'Pulseiras',
                                    'icon' => 'id-card',
                                    'url' => ['/pulseira/index']
                            ],
                            [
                                    'label' => 'Consultas',
                                    'icon' => 'notes-medical',
                                    'url' => ['/consulta/index']
                            ],
                            [
                                    'label' => 'Prescrições',
                                    'icon' => 'prescription-bottle-alt',
                                    'url' => ['/prescricao/index']
                            ],
                            [
                                    'label' => 'Notificações',
                                    'icon' => 'bell',
                                    'url' => ['/notificacao/index']
                            ],
                        // 🔹 ALTERADO: Agora abre o perfil do utilizador autenticado
                            [
                                    'label' => 'Perfil',
                                    'icon' => 'user-cog',
                                    'url' => ['/user-profile/meu-perfil'],
                                    'visible' => !Yii::$app->user->isGuest
                            ],
                            [
                                    'label' => 'Sair',
                                    'icon' => 'sign-out-alt',
                                    'url' => ['/site/logout'],
                                    'visible' => !Yii::$app->user->isGuest,
                                    'template' => '<a href="{url}" data-method="post">{icon}{label}</a>',
                            ],
                    ],
            ]);
            ?>
        </nav>
    </div>
</aside>
