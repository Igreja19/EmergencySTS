<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */

$this->title = 'Editar Pulseira';
$this->params['breadcrumbs'][] = ['label' => 'Pulseiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Ver Pulseira #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';

$this->registerCss('
.pulseira-update {
    max-width: 900px;
    margin: 0 auto;
}
.pulseira-update h1 {
    color: #198754;
    font-weight: 700;
    margin-bottom: 25px;
    text-align: center;
}
.pulseira-form {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 25px 30px;
    margin-bottom: 25px;
}
.pulseira-form h5 {
    color: #198754;
    font-weight: 700;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-back {
    background: #198754;
    color: #fff;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    transition: .2s;
}
.btn-back:hover {
    opacity: .9;
    transform: translateY(-2px);
}
');
?>

<div class="pulseira-update">
    <h1><i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?></h1>

    <div class="pulseira-form">
        <?= $this->render('_form', [
                'model' => $model,
        ]) ?>
    </div>

    <div class="text-center mt-3">
        <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-back']) ?>
    </div>
</div>
