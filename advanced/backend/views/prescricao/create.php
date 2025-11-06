<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */
/** @var array $consultas */

$this->title = 'Criar Prescrição';
$this->params['breadcrumbs'][] = ['label' => 'Prescrições', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prescricao-create">
    <h1 class="text-success fw-bold mb-3">
        <i class="bi bi-plus-circle me-2"></i><?= Html::encode($this->title) ?>
    </h1>

    <?= $this->render('_form', [
            'model' => $model,
            'consultas' => $consultas,   // ✅ só consultas (sem $medicamentos)
    ]) ?>
</div>
