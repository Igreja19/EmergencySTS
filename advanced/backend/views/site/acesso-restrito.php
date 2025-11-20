<?php
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/site/acesso-restrito.css');

$this->title = "Acesso Restrito";
?>

<div class="login-box premium-container" style="width: 480px;">

    <div class="card premium-card">

        <div class="card-header text-center" style="border-bottom: none;">

            <!-- Ícone premium -->
            <div class="premium-icon-container">
                <i class="fas fa-ban premium-icon"></i>
            </div>

            <h3 class="premium-title">Acesso Restrito</h3>
        </div>

        <div class="card-body text-center" style="padding-top: 0;">

            <p class="premium-text">
                Esta área é exclusiva para funcionários do hospital.
            </p>

            <p id="contador-texto" class="premium-counter">
                Será redirecionado em <b id="contador">10</b> segundos...
            </p>

            <!-- 🔥 BOTÃO CORRIGIDO → FRONTEND -->
            <a href="/PLATF/EmergencySTS/advanced/frontend/web/index.php" class="premium-button">
                <i class="fas fa-home mr-2"></i> Ir para página inicial agora
            </a>

        </div>
    </div>
</div>

<!-- 🔥 Script Redirecionamento corrigido → FRONTEND -->
<?php
$this->registerJs("
    var s = 10;
    var intv = setInterval(function() {
        s--;
        $('#contador').text(s);
        if (s <= 0) {
            clearInterval(intv);
            window.location.href = '/PLATF/EmergencySTS/advanced/frontend/web/index.php';
        }
    }, 1000);
");
?>
