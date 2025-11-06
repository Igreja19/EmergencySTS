<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */

$this->title = 'Detalhes da Prescrição #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prescrições', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prescricao-view">

    <h1 class="text-success fw-bold mb-3">
        <i class="bi bi-file-medical me-2"></i><?= Html::encode($this->title) ?>
    </h1>

    <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                    'id',
                    'observacoes',
                    'dataprescricao',
                    'consulta_id',
            ],
    ]) ?>

    <p class="mt-3">
        <?= Html::a('<i class="bi bi-pencil-square"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="bi bi-arrow-left-circle"></i> Voltar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>
</div>
