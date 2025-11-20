<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use hail812\adminlte3\assets\AdminLteAsset;
use hail812\adminlte3\assets\PluginAsset;
use yii\web\JqueryAsset;

AdminLteAsset::register($this);
PluginAsset::register($this)->add(['fontawesome', 'icheck-bootstrap']);

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

    <!-- 🎨 Fundo Premium -->
    <style>
        /* === FUNDO GRADIENTE SUAVE === */
        #background-gradient {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: linear-gradient(
                    120deg,
                    rgba(240, 255, 248, 0.7) 0%,
                    rgba(200, 255, 220, 0.6) 40%,
                    rgba(25, 135, 84, 0.25) 100%
            );
            background-size: 200% 200%;
            animation: subtleFade 20s ease-in-out infinite;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: background 1s ease;
        }

        #background-gradient::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(
                    circle at 70% 30%,
                    rgba(255, 255, 255, 0.3),
                    transparent 70%
            );
            mix-blend-mode: soft-light;
            pointer-events: none;
        }

        @keyframes subtleFade {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>

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
