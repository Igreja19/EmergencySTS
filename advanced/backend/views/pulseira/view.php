<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */

$this->title = 'Detalhes da Pulseira #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pulseiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/pulseira/view.css');
?>

<div class="pulseira-view">
    <div class="view-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h3><i class="bi bi-upc-scan me-2"></i><?= Html::encode($this->title) ?></h3>
            <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-back']) ?>
        </div>

        <!-- ================================
             DETALHES DA PULSEIRA
        ================================= -->
        <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                        'id',
                        [
                                'attribute' => 'codigo',
                                'label' => 'Código da Pulseira',
                                'format' => 'text',
                        ],
                        [
                                'attribute' => 'prioridade',
                                'label' => 'Prioridade',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $cor = $model->prioridade ?? '-';
                                    return $cor ? "<span class='badge-prio badge-{$cor}'>{$cor}</span>" : '-';
                                },
                        ],
                        [
                                'attribute' => 'tempoentrada',
                                'label' => 'Tempo de Entrada',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                        ],
                        [
                                'attribute' => 'status',
                                'label' => 'Estado',
                                'value' => function ($model) {
                                    return match ($model->status) {
                                        'Em espera' => '⏳ A aguardar Atendimento',
                                        'Em atendimento' => '🩺 Em Atendimento',
                                        'Atendido' => '✅ Atendido',
                                        default => Html::encode($model->status),
                                    };
                                },
                        ],
                        [
                                'label' => 'Paciente',
                                'value' => $model->userprofile->nome ?? '—',
                        ],
                        [
                                'label' => 'Triagem Associada',
                                'format' => 'html',
                                'value' => $model->triagem
                                        ? Html::a(
                                                'Ver Triagem #' . $model->triagem->id,
                                                ['triagem/view', 'id' => $model->triagem->id],
                                                ['class' => 'text-success fw-semibold']
                                        )
                                        : '—',
                        ],
                ],
        ]) ?>

        <!-- ================================
             DADOS COMPLETOS DA TRIAGEM
        ================================= -->
        <?php if ($model->triagem): ?>
            <hr class="my-4">

            <h4 class="triagem-title">
                <i class="bi bi-clipboard-pulse"></i>
                Dados da Triagem
            </h4>

            <?= DetailView::widget([
                    'model' => $model->triagem,
                    'attributes' => [
                            'id',
                            [
                                    'attribute' => 'datatriagem',
                                    'label' => 'Data da Triagem',
                                    'format' => ['datetime', 'php:d/m/Y H:i']
                            ],
                            'motivoconsulta',
                            'queixaprincipal',
                            'descricaosintomas:ntext',
                            'iniciosintomas',
                            'alergias:ntext',
                            'medicacao:ntext',
                            [
                                    'label' => 'Paciente',
                                    'value' => $model->triagem->userprofile->nome ?? '—'
                            ],
                    ],
            ]) ?>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <?= Html::a('<i class="bi bi-pencil-square me-1"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-warning text-white me-2']) ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i> Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                            'confirm' => 'Tens a certeza que queres eliminar esta pulseira?',
                            'method' => 'post',
                    ],
            ]) ?>
        </div>
    </div>
</div>
