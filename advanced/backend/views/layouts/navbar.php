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

        <!-- Home -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::home() ?>" class="nav-link">Home</a>


        </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3" method="get" action="<?= Url::to(['/site/index']) ?>">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" name="q" placeholder="Pesquisar..." aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- User dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-user"></i>
                <?= $user ? Html::encode($user->username) : 'Conta' ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?= Url::to(['/user-profile/index']) ?>" class="dropdown-item">
                    <i class="fas fa-id-badge mr-2"></i> Perfil
                </a>
                <div class="dropdown-divider"></div>
                <?= Html::a('<i class="fas fa-sign-out-alt mr-2"></i> Terminar sessão', ['/site/logout'], [
                        'class' => 'dropdown-item',
                        'data-method' => 'post'
                ]) ?>
            </div>
        </li>

        <!-- Fullscreen -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Ecrã inteiro">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <!-- Dark mode -->
        <li class="nav-item">
            <a class="nav-link" href="#" id="darkToggle" title="Tema escuro">
                <i class="fas fa-moon"></i>
            </a>
        </li>

        <!-- Control sidebar (opcional) -->
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button" title="Painel lateral">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<?php
// Dark mode persistente (localStorage)
$js = <<<JS
(function(){
  const key = 'emergencysts-theme';
  const saved = localStorage.getItem(key);
  if(saved === 'dark') document.body.classList.add('dark-mode');
  const toggle = document.getElementById('darkToggle');
  if(toggle){
    toggle.addEventListener('click', function(e){
      e.preventDefault();
      document.body.classList.toggle('dark-mode');
      localStorage.setItem(key, document.body.classList.contains('dark-mode') ? 'dark' : 'light');
    });
  }
})();
JS;
$this->registerJs($js);
?>
