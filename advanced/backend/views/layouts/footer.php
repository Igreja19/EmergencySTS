<?php
use yii\helpers\Url;
?>

<footer class="main-footer dark">
    <div class="row w-100 m-0">
        <!-- ESQUERDA -->
        <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
            <small>
                © <span class="text-success fw-semibold">EmergencySTS</span>
                <?= date('Y') ?>. Todos os direitos reservados.
            </small>
        </div>

        <!-- DIREITA -->
        <div class="col-md-6 text-center text-md-end">
            <small>
                Desenvolvido por
                <a href="<?= Url::to(['/team/index']) ?>"
                   class="text-success text-decoration-none fw-semibold">
                    EmergencySTS Dev Team
                </a>
            </small>
        </div>
    </div>
</footer>
