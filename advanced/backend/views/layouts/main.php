<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

// 1) AdminLTE base
\hail812\adminlte3\assets\AdminLteAsset::register($this);

// 2) (Opcional) Plugins em bundles próprios — sem usar ->add()
if (class_exists(\hail812\adminlte3\assets\FontAwesomeAsset::class)) {
    \hail812\adminlte3\assets\FontAwesomeAsset::register($this);
}
if (class_exists(\hail812\adminlte3\assets\ICheckBootstrapAsset::class)) {
    \hail812\adminlte3\assets\ICheckBootstrapAsset::register($this);
}

// 3) Fonts + CSS do teu tema
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');
$this->registerCssFile('/css/adminlte-custom.css', ['depends' => [\yii\web\JqueryAsset::class]]);

// 4) (Opcional) Se precisares de paths do tema original AdminLTE (almasaeed)
$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

// 5) control_sidebar.js do hail812 (com depends em array)
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
        <?php $this->head() ?>
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
    <?php $this->beginBody() ?>

    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->render('navbar', ['assetDir' => $assetDir]) ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?= $this->render('sidebar', ['assetDir' => $assetDir]) ?>

        <!-- Content Wrapper. Contains page content -->
        <?= $this->render('content', ['content' => $content, 'assetDir' => $assetDir]) ?>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <?= $this->render('control-sidebar') ?>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <?= $this->render('footer') ?>
    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>