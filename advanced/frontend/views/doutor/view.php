<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Doctor $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Médicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="row g-0">
            <div class="col-md-4">
                <?= Html::img(
                    $model->photo ? Yii::getAlias('@web/uploads/doctors/' . $model->photo) : '@web/img/default-doctor.jpg',
                    ['class' => 'img-fluid h-100 w-100 object-fit-cover', 'alt' => $model->name]
                ) ?>
            </div>
            <div class="col-md-8">
                <div class="card-body p-5">
                    <h2 class="fw-bold text-success mb-3"><?= Html::encode($model->name) ?></h2>
                    <h5 class="text-muted mb-4"><?= Html::encode($model->speciality) ?></h5>

                    <?php if (!empty($model->bio)): ?>
                        <p class="text-secondary mb-4"><?= nl2br(Html::encode($model->bio)) ?></p>
                    <?php endif; ?>

                    <div class="d-flex flex-column flex-md-row gap-3">
                        <?php if (!empty($model->email)): ?>
                            <a href="mailto:<?= Html::encode($model->email) ?>" class="btn btn-outline-success px-4">
                                <i class="bi bi-envelope me-2"></i> Contactar
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($model->phone)): ?>
                            <a href="tel:<?= Html::encode($model->phone) ?>" class="btn btn-outline-success px-4">
                                <i class="bi bi-telephone me-2"></i> Telefonar
                            </a>
                        <?php endif; ?>

                        <a href="<?= Yii::$app->urlManager->createUrl(['site/index']) ?>" class="btn btn-light border px-4">
                            <i class="bi bi-arrow-left me-2"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
