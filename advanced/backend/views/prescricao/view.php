<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */

$this->title = "Prescrição #" . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prescrições', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>
<div class="prescricao-view">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold text-success mb-0">
            <i class="bi bi-file-earmark-medical me-2"></i>
            <?= Html::encode($this->title) ?>
        </h1>

        <div class="d-flex gap-2">
            <?= Html::a('<i class="bi bi-pencil"></i> Editar', ['update', 'id' => $model->id], [
                    'class' => 'btn btn-primary'
            ]) ?>

            <?= Html::a('<i class="bi bi-trash"></i> Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                            'confirm' => 'Tem a certeza que deseja eliminar esta prescrição?',
                            'method' => 'post',
                    ],
            ]) ?>
        </div>
    </div>

    <div class="card p-4 shadow-sm rounded-4">
        <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                        'id',
                        [
                                'attribute' => 'observacoes',
                                'format' => 'ntext',
                                'label' => 'Observações'
                        ],
                        [
                                'attribute' => 'dataprescricao',
                                'format' => ['date', 'php:d/m/Y H:i'],
                                'label' => 'Data da Prescrição'
                        ],
                        [
                                'attribute' => 'consulta_id',
                                'label' => 'Consulta',
                                'value' => fn($m) => "Consulta #" . $m->consulta_id
                        ],
                ],
        ]) ?>
    </div>

</div>
