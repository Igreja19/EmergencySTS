<?php

use yii\helpers\Url;

$this->title = "Acesso Restrito";
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/site/acesso-restrito.css');
?>

<div class="login-box premium-container">

    <div class="card premium-card">

        <div class="card-header text-center">

            <!-- Ícone premium -->
            <div class="premium-icon-container">
                <i class="fas fa-ban premium-icon"></i>
            </div>

            <h3 class="premium-title">Acesso Restrito</h3>
        </div>

        <div class="card-body text-center">

            <p class="premium-text">
                Esta área é exclusiva para funcionários do hospital.
            </p>

            <p id="contador-texto" class="premium-counter">
                Será redirecionado em <b id="contador">10</b> segundos...
            </p>

            <div id="redirect-config"
                 data-url="<?= Url::to(['site/index']) ?>">
            </div>
            <!-- 🔥 BOTÃO CORRIGIDO → FRONTEND -->
            <a href="<?= Url::to(['site/index']) ?>" class="premium-button">
                <i class="fas fa-home mr-2"></i> Ir para página inicial agora
            </a>

        </div>
    </div>
</div>

<?php
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/site/acesso-restrito.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
