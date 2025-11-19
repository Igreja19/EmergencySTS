<?php
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/prescricao/_search.css');

?>
<div class="userprofile-search">
    <form method="get">
        <div class="row g-3 align-items-center">

            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-success"></i>
                    </span>
                    <input type="date"
                           name="PrescricaoSearch[dataprescricao]"
                           class="form-control border-start-0"
                           style="border-radius: 0 12px 12px 0;">
                </div>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-success px-4 fw-semibold">
                    <i class="bi bi-search me-1"></i> Procurar
                </button>

                <a href="<?= Yii::$app->request->baseUrl ?>/prescricao/index"
                   class="btn btn-outline-secondary px-4 fw-semibold">
                    <i class="bi bi-x-lg me-1"></i> Limpar
                </a>
            </div>

        </div>
    </form>
</div>
