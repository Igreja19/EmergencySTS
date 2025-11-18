<?php
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

<!-- 🎨 CSS PREMIUM -->
<style>

    /* Animação entrada premium */
    .premium-container {
        animation: fadeSlideIn 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    @keyframes fadeSlideIn {
        to { opacity: 1; transform: translateY(0); }
    }

    /* Card premium */
    .premium-card {
        border-radius: 22px;
        padding-top: 20px;
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        background: rgba(255,255,255,0.85);
        box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    }

    /* Ícone circular premium */
    .premium-icon-container {
        width: 95px;
        height: 95px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
        animation: iconPulse 2.5s infinite ease-in-out;
    }

    .premium-icon {
        font-size: 46px;
        color: #d9534f;
    }

    @keyframes iconPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.06); }
        100% { transform: scale(1); }
    }

    .premium-title {
        font-weight: 700;
        color: #c0392b;
        margin-bottom: 5px;
        font-size: 26px;
    }

    .premium-text {
        font-size: 16px;
        color: #444;
        margin: 10px 15px;
    }

    .premium-counter {
        font-size: 14px;
        color: #666;
        margin-top: 10px;
    }

    .premium-button {
        display: block;
        background: linear-gradient(90deg, #0d6efd, #0a58ca);
        padding: 12px 0;
        border-radius: 12px;
        color: white !important;
        font-size: 17px;
        margin-top: 20px;
        transition: 0.2s ease;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(13,110,253,0.25);
    }
    .premium-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13,110,253,0.35);
    }

</style>
