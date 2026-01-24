<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

\hail812\adminlte3\assets\AdminLteAsset::register($this);
\hail812\adminlte3\assets\PluginAsset::register($this);

if (class_exists(\hail812\adminlte3\assets\FontAwesomeAsset::class)) {
    \hail812\adminlte3\assets\FontAwesomeAsset::register($this);
}
if (class_exists(\hail812\adminlte3\assets\ICheckBootstrapAsset::class)) {
    \hail812\adminlte3\assets\ICheckBootstrapAsset::register($this);
}

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/layouts/main.css');
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');
$this->registerCssFile(Yii::getAlias('@web') . '/css/adminlte-custom.css?v=1.1');
$this->registerCssFile(Yii::getAlias('@web') . '/css/sidebar.css?v=1.0');

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="icon" type="image/png" href="<?= Yii::$app->request->baseUrl ?>/img/logo.png">

    <?php $this->head() ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<audio id="notifSound" src="/pws/EmergencySTS/advanced/backend/web/sounds/notificacao.mp3" preload="auto"></audio>

<?php
$auth = Yii::$app->authManager;
$userId = Yii::$app->user->id ?? null;
$roles = $userId ? array_keys($auth->getRolesByUser($userId)) : [];

$isAdmin      = in_array('admin', $roles);
$isMedico     = in_array('medico', $roles);
$isEnfermeiro = in_array('enfermeiro', $roles);
?>

<?php $this->beginBody() ?>

<div id="toast-container" style="position:fixed; top:20px; right:20px; z-index:999999;"></div>

<div class="wrapper">

    <?= $this->render('navbar', compact('assetDir','isAdmin','isMedico','isEnfermeiro')) ?>

    <?= $this->render('sidebar', compact('assetDir','isAdmin','isMedico','isEnfermeiro')) ?>

    <?= $this->render('content', ['content' => $content]) ?>
    <?= $this->render('control-sidebar') ?>
    <?= $this->render('footer') ?>

</div>

<script>
    document.addEventListener("scroll", function () {
        const header = document.querySelector(".sticky-header");
        if (header) header.classList.toggle("scrolled", window.scrollY > 10);
    });
</script>

<!-- 🔥 SSE NOTIFICAÇÕES -->
<script>
    let ultimoCount = 0;

    function ligarSSE() {

        const evtSource = new EventSource(
            "http://localhost/platf/EmergencySTS/advanced/backend/web/notificacao/stream"
        );

        evtSource.onmessage = function(event) {
            processarNotificacoes(event.data);
        };

        evtSource.onerror = function() {
            console.warn("🔌 SSE caiu, a reconectar...");
            evtSource.close();
            setTimeout(ligarSSE, 3000);
        };
    }

    function processarNotificacoes(rawData) {
        const data = JSON.parse(rawData);
        const count = data.length;

        const bell = document.querySelector(".notif-btn");
        const list = document.querySelector(".notif-body");
        const headerBadge = document.querySelector(".notif-header .badge");
        const audio = document.getElementById("notifSound");

        if (!list) return;

        list.innerHTML = "";

        if (count === 0) {

            list.innerHTML = `
            <div class='text-center text-muted py-3'>
                <i class='bi bi-inbox fs-2 mb-2'></i>
                <small>Sem novas notificações</small>
            </div>`;

            if (headerBadge) headerBadge.remove();
            const red = bell?.querySelector(".notif-badge");
            if (red) red.remove();

            return;
        }

        if (!bell.querySelector(".notif-badge")) {
            bell.insertAdjacentHTML("beforeend", "<span class='notif-badge'></span>");
        }

        if (headerBadge) {
            headerBadge.textContent = count;
        } else {
            document.querySelector(".notif-header").insertAdjacentHTML(
                "beforeend",
                `<span class='badge bg-success rounded-pill'>${count}</span>`
            );
        }

        data.forEach(n => {
            list.insertAdjacentHTML("beforeend", `
                <div class='notif-item d-flex p-2 mb-1 rounded-3'>
                    <div class='notif-icon me-2'>
                        <i class='bi bi-exclamation-circle-fill text-success fs-5'></i>
                    </div>
                    <div class='flex-grow-1'>
                        <div class='fw-semibold'>${n.titulo}</div>
                        <div class='text-muted small'>${n.mensagem}</div>
                    </div>
                </div>
            `);
        });

        if (count > ultimoCount) {
            audio.currentTime = 0;
            audio.play();
        }

        ultimoCount = count;
    }

    //ligarSSE();
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
