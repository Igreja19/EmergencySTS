<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Consulta #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Consultas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/consulta/view.css');

?>

<div class="view-box">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="title-header">
            <i class="bi bi-journal-medical"></i>
            <?= Html::encode($this->title) ?>
        </h3>

        <?= Html::a('<i class="bi bi-arrow-left"></i> Voltar', ['index'], ['class' => 'btn-back']) ?>
    </div>

    <!-- Detalhes da Consulta -->
    <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-bordered table-detail'],
            'attributes' => [
                    'id',
                    [
                            'attribute' => 'data_consulta',
                            'format' => ['datetime', 'php:d/m/Y H:i']
                    ],

                    [
                            'attribute' => 'estado',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return "<span class='badge-estado badge-{$model->estado}'>" . ucfirst($model->estado) . "</span>";
                            }
                    ],

                    'observacoes:ntext',

                    [
                            'label' => 'Paciente',
                            'value' => $model->userprofile->nome ?? '-'
                    ],

                    [
                            'label' => 'Triagem',
                            'format' => 'html',
                            'value' => Html::a(
                                    'Ver Triagem #' . $model->triagem->id,
                                    ['triagem/view', 'id' => $model->triagem->id],
                                    ['class' => 'text-success fw-bold']
                            )
                    ],

                    [
                            'label' => 'Pulseira',
                            'format' => 'html',
                            'value' => $model->triagem && $model->triagem->pulseira
                                    ? Html::a(
                                            $model->triagem->pulseira->codigo,
                                            ['pulseira/view', 'id' => $model->triagem->pulseira->id],
                                            ['class' => 'text-success fw-bold']
                                    )
                                    : '-'
                    ],

                    [
                            'attribute' => 'data_encerramento',
                            'format' => ['datetime', 'php:d/m/Y H:i'],
                            'value' => $model->data_encerramento ?? '-'
                    ],

                    'relatorio_pdf',
            ]
    ]) ?>

</div>

<!-- ==========================
       PRESCRIÇÕES
========================== -->
<div class="prescricao-box">

    <h4 class="text-success fw-bold mb-3">
        <i class="bi bi-capsule-pill"></i> Prescrições
    </h4>

    <?php if (empty($model->prescricaos)): ?>

        <p class="text-muted">Nenhuma prescrição disponível.</p>

    <?php else: ?>

        <?php foreach ($model->prescricaos as $p): ?>

            <div class="border rounded p-3 mb-2 d-flex justify-content-between">
                <div>
                    <strong>Prescrição #<?= $p->id ?></strong><br>
                    <?= $p->tipo ?? 'Sem tipo' ?>
                </div>

                <?= Html::a('Ver', ['prescricao/view', 'id' => $p->id], ['class' => 'btn btn-outline-success btn-sm']) ?>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
