<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

// AdminLTE
\hail812\adminlte3\assets\AdminLteAsset::register($this);

// Plugins opcionais
if (class_exists(\hail812\adminlte3\assets\FontAwesomeAsset::class)) {
    \hail812\adminlte3\assets\FontAwesomeAsset::register($this);
}
if (class_exists(\hail812\adminlte3\assets\ICheckBootstrapAsset::class)) {
    \hail812\adminlte3\assets\ICheckBootstrapAsset::register($this);
}

// CSS custom
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/main.css');
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');
$this->registerCssFile(Yii::getAlias('@web') . '/css/navbar.css?v=1.1');

// ⭐ ADICIONADO: CSS DO DASHBOARD PREMIUM V2
$this->registerCssFile(Yii::getAlias('@web') . '/css/admin.css?v=1.0');

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$publishedRes = Yii::$app->assetManager->publish('@vendor/hail812/yii2-adminlte3/src/web/js');
$this->registerJsFile($publishedRes[1] . '/control_sidebar.js');
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="icon" type="image/png" href="<?= Yii::$app->request->baseUrl ?>/img/logo.png">

    <?php $this->head() ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<!-- 🔊 Som das notificações -->
<div id="config"
     data-sse="<?= \yii\helpers\Url::to(['/notificacao-stream/index'], true) ?>"
     data-sound="<?= \yii\helpers\Url::to('@web/sounds/notificacao.mp3', true) ?>">
</div>

<audio id="notifSound" preload="auto"></audio>

<?php $this->beginBody() ?>

<div id="toast-container"></div>

<div class="wrapper">

    <?= $this->render('navbar', ['assetDir' => $assetDir]) ?>
    <?= $this->render('sidebar', ['assetDir' => $assetDir]) ?>
    <?= $this->render('content', ['content' => $content, 'assetDir' => $assetDir]) ?>
    <?= $this->render('control-sidebar') ?>
    <?= $this->render('footer') ?>

</div>
<?php $this->endBody() ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $this->endPage() ?>
<?php
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/layouts/main.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
