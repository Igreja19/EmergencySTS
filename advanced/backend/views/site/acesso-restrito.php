<?php

use yii\helpers\Html;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/site/acesso-restrito.css');

$this->title = "Acesso Restrito";
?>

<script>
    window.redirectHomeUrl = <?= json_encode(Yii::$app->homeUrl) ?>;
</script>

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

            <?= Html::a(
                    '<i class="fas fa-home me-2"></i> Ir para página inicial agora',
                    ['site/index'],
                    ['class' => 'premium-button']
            ) ?>


        </div>
    </div>
</div>

<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/js/site/acesso-restrito.js?v=123',
        ['depends' => [\yii\web\JqueryAsset::class]]
);
?>
