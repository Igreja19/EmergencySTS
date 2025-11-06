<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */
/** @var array $consultas */

$this->title = 'Editar Prescrição #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prescrições', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Ver Prescrição #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="prescricao-update">
    <h1 class="text-success fw-bold mb-3">
        <i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?>
    </h1>

    <?= $this->render('_form', [
            'model' => $model,
            'consultas' => $consultas,   // ✅ só consultas
    ]) ?>
</div>
