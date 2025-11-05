<?php
use hail812\adminlte\widgets\Menu;
use yii\helpers\Url;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/sidebar.css');

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= Url::to(['/site/index']) ?>" class="brand-link">
        <img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"
             alt="EmergencySTS"
             class="brand-image img-circle elevation-3"
             style="opacity:.9">
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
                            ['label' => 'Dashboard',     'icon' => 'tachometer-alt',          'url' => ['/site/index'],          'options' => ['class' => 'is-dashboard']],
                            ['label' => 'Utilizadores',  'icon' => 'users',                    'url' => ['/user-profile/index'],  'options' => ['class' => 'is-users']],
                            ['label' => 'Triagem',       'icon' => 'stethoscope',              'url' => ['/triagem/index'],       'options' => ['class' => 'is-triagem']],
                            ['label' => 'Pulseiras',     'icon' => 'id-card',                  'url' => ['/pulseira/index'],      'options' => ['class' => 'is-pulseira']],
                            ['label' => 'Consultas',     'icon' => 'notes-medical',            'url' => ['/consulta/index'],      'options' => ['class' => 'is-consulta']],
                            ['label' => 'Prescrições',   'icon' => 'prescription-bottle-alt',  'url' => ['/prescricao/index'],    'options' => ['class' => 'is-prescricao']],
                            ['label' => 'Notificações',  'icon' => 'bell',                     'url' => ['/notificacao/index'],   'options' => ['class' => 'is-notificacao']],
                            [
                                    'label' => 'Perfil', 'icon' => 'user-cog', 'url' => ['/user-profile/meu-perfil'],
                                    'visible' => !Yii::$app->user->isGuest, 'options' => ['class' => 'is-perfil']
                            ],
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
                                    'options' => ['class' => 'is-sair']
                            ],
                    ],
            ]);
            ?>
        </nav>
    </div>
</aside>

