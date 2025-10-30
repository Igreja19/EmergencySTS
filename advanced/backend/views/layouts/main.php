<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

// 1) AdminLTE base
\hail812\adminlte3\assets\AdminLteAsset::register($this);

// 2) Plugins opcionais (FontAwesome, iCheck, etc.)
if (class_exists(\hail812\adminlte3\assets\FontAwesomeAsset::class)) {
    \hail812\adminlte3\assets\FontAwesomeAsset::register($this);
}
if (class_exists(\hail812\adminlte3\assets\ICheckBootstrapAsset::class)) {
    \hail812\adminlte3\assets\ICheckBootstrapAsset::register($this);
}

// 3) Fonts + CSS customizado
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');
$this->registerCssFile(Yii::getAlias('@web') . '/css/adminlte-custom.css?v=1.1', ['depends' => [\yii\web\JqueryAsset::class]]);
// 4) Diretório base AdminLTE
$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

// 5) Control Sidebar script
$publishedRes = Yii::$app->assetManager->publish('@vendor/hail812/yii2-adminlte3/src/web/js');
$this->registerJsFile($publishedRes[1] . '/control_sidebar.js', [
        'depends' => [\hail812\adminlte3\assets\AdminLteAsset::class],
]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <!-- ✅ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <?php $this->head() ?>

    <!-- 💅 Tema hospitalar verde -->
    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: #f4f6f9;
        }
        .main-header.navbar {
            background: linear-gradient(90deg, #198754, #20c997) !important;
            color: #fff;
        }
        .main-sidebar {
            background-color: #1e1e1e !important;
        }
        .main-footer {
            background-color: #f8f9fa !important;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .btn-success, .bg-success {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }
        a {
            color: #198754;
        }
        a:hover {
            color: #146c43;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<?php $this->beginBody() ?>

<div class="wrapper">

    <!-- Navbar -->
    <?= $this->render('navbar', ['assetDir' => $assetDir]) ?>

    <!-- Sidebar -->
    <?= $this->render('sidebar', ['assetDir' => $assetDir]) ?>

    <!-- Conteúdo principal -->
    <?= $this->render('content', ['content' => $content, 'assetDir' => $assetDir]) ?>

    <!-- Control Sidebar -->
    <?= $this->render('control-sidebar') ?>

    <!-- Rodapé -->
    <?= $this->render('footer') ?>

</div>

<!--  Tooltips Bootstrap -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    });
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
