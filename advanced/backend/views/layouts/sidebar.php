<?php
use hail812\adminlte\widgets\Menu;
use yii\helpers\Url;
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
                                    'label' => 'Sair', 'icon' => 'sign-out-alt', 'url' => ['/site/logout'],
                                    'visible' => !Yii::$app->user->isGuest,
                                    'template' => '<a href="{url}" data-method="post">{icon}{label}</a>',
                                    'options' => ['class' => 'is-sair']
                            ],
                    ],
            ]);
            ?>
        </nav>
    </div>
</aside>

<style>
    .nav-sidebar .nav-item .nav-icon { transition: transform .15s ease; }

    .nav-sidebar .is-dashboard  .nav-icon { color: #20c997 !important; } /* teal */
    .nav-sidebar .is-users      .nav-icon { color: #6f42c1 !important; } /* roxo */
    .nav-sidebar .is-triagem    .nav-icon { color: #fd7e14 !important; } /* laranja */
    .nav-sidebar .is-pulseira   .nav-icon { color: #0d6efd !important; } /* azul */
    .nav-sidebar .is-consulta   .nav-icon { color: #0dcaf0 !important; } /* ciano */
    .nav-sidebar .is-prescricao .nav-icon { color: #6610f2 !important; } /* índigo */
    .nav-sidebar .is-notificacao .nav-icon{ color: #ffc107 !important; } /* amarelo */
    .nav-sidebar .is-perfil     .nav-icon { color: #e83e8c !important; } /* rosa */
    .nav-sidebar .is-sair       .nav-icon { color: #adb5bd !important; } /* cinza */

    /* efeito hover: leve zoom */
    .nav-sidebar .nav-item:hover .nav-icon { transform: scale(1.08); }

    /* quando ativo, mantém a cor do ícone e destaca o link */
    .nav-sidebar .nav-item .nav-link.active {
        background: rgba(255,255,255,0.12);
        font-weight: 600;
    }
</style>