<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Detalhes da Consulta';
$this->params['breadcrumbs'][] = ['label' => 'Consultas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="consulta-view card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-success mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Consulta #<?= Html::encode($model->id) ?></h5>
        <div>
            <?= Html::a('<i class="bi bi-pencil-square me-1"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
            <?= Html::a('<i class="bi bi-trash3 me-1"></i> Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-outline-danger btn-sm',
                    'data' => ['confirm' => 'Tem certeza que deseja eliminar esta consulta?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-borderless align-middle'],
            'attributes' => [
                    'id',
                    'data_consulta:datetime',
                    'estado',
                    'observacoes:ntext',
                    [
                            'label' => 'Paciente',
                            'value' => $model->userprofile->nome ?? '-',
                    ],
                    [
                            'label' => 'Triagem',
                            'value' => $model->triagem->id ?? '-',
                    ],
                    [
                            'label' => 'Prescrição',
                            'value' => $model->prescricao->id ?? '-',
                    ],
                    'data_encerramento:datetime',
                    'relatorio_pdf',
            ],
    ]) ?>
</div>
