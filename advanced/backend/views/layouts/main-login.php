<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use hail812\adminlte3\assets\AdminLteAsset;
use hail812\adminlte3\assets\PluginAsset;
use yii\web\JqueryAsset;

AdminLteAsset::register($this);
PluginAsset::register($this)->add(['fontawesome', 'icheck-bootstrap']);
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/main-login.css');

$this->registerCssFile('https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap');
$this->registerCssFile(Yii::getAlias('@web') . '/css/adminlte-custom.css?v=1.2', ['depends' => [JqueryAsset::class]]);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#198754">
    <link rel="icon" type="image/png" href="<?= Yii::getAlias('@web') ?>/img/logo.png">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title ?: 'EmergencySTS | Acesso Restrito') ?></title>

    <?php $this->head() ?>
</head>

<body class="hold-transition login-page" style="background: transparent !important; overflow: hidden;">
<?php $this->beginBody() ?>

<!-- 🌿 Fundo Animado -->
<div id="background-gradient"></div>

<!-- 🔹 Container do conteúdo -->
<main class="login-container d-flex align-items-center justify-content-center" style="z-index: 10; position: relative;">
    <?= $content ?>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
