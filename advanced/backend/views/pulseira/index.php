<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;


$this->title = 'Pulseiras';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/pulseira/index.css');


?>

<div class="pulseira-index">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title"><i class="bi bi-upc-scan"></i> Pulseiras</h1>
        <?php if ($isAdmin): ?>
            <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Nova Pulseira', ['create'], [
                    'class' => 'btn-new'
            ]) ?>
        <?php endif ?>
    </div>

    <!-- SEARCH + TABLE CARD -->
    <div class="card-box">

        <!-- Filtros estilo Prescrições -->
        <div class="mb-3">
            <?= $this->render('_search', ['model' => $searchModel]) ?>
        </div>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-modern align-middle'],
                'columns' => [

                        ['class' => 'yii\grid\SerialColumn', 'header' => '#'],

                        [
                                'attribute' => 'id',
                                'label' => 'ID',
                                'headerOptions' => ['style' => 'width:70px;'],
                        ],

                        [
                                'attribute' => 'codigo',
                                'label' => 'Código da Pulseira',
                        ],

                        [
                                'attribute' => 'prioridade',
                                'label' => 'Prioridade',
                                'format' => 'raw',
                                'value' => function ($m) {
                                    if (!$m->prioridade) {
                                        return "<span class='text-secondary'>—</span>";
                                    }
                                    return "<span class='badge-prio badge-{$m->prioridade}'>{$m->prioridade}</span>";
                                }
                        ],

                        [
                                'attribute' => 'tempoentrada',
                                'label' => 'Entrada',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                        ],

                        [
                                'label' => 'Paciente',
                                'value' => fn($m) => $m->userprofile->nome ?? '—'
                        ],

                        [
                                'label' => 'Triagem',
                                'value' => fn($m) => $m->triagem->motivoconsulta ?? '—'
                        ],

                        [
                                'attribute' => 'status',
                                'value' => function ($m) {
                                    return match ($m->status) {
                                        'Em espera' => '⏳ A aguardar Atendimento',
                                        'Em atendimento' => '🩺 Em Atendimento',
                                        'Atendido' => '✅ Atendido',
                                        default => $m->status
                                    };
                                }
                        ],

                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => Yii::$app->user->can('admin')
                                        ? '{view} {update} {delete}'
                                        : '{view} {update}',
                                'contentOptions' => ['style' => 'text-align:center;'],
                                'buttons' => [
                                        'view' => fn($url) =>
                                        Html::a('<i class="bi bi-eye"></i>', $url, ['class'=>'btn-action btn-view']),

                                        'update' => fn($url) =>
                                        Html::a('<i class="bi bi-pencil"></i>', $url, ['class'=>'btn-action btn-edit']),

                                        'delete' => fn($url) =>
                                        Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class'=>'btn-action btn-delete',
                                                'data-confirm'=>'Tens a certeza?',
                                                'data-method'=>'post'
                                        ]),
                                ],
                        ],
                ],
        ]) ?>

        <?php Pjax::end(); ?>
    </div>
</div>
