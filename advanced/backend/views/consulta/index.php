<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\ConsultaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Consultas';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/consulta/index.css');
?>

<div class="consulta-index">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mb-0"><i class="bi bi-clipboard2-pulse me-2"></i><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Nova Consulta', ['create'], ['class' => 'btn btn-new']) ?>
    </div>

    <div class="card-table">
        <div class="mb-3">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'tableOptions' => ['class' => 'table table-striped table-modern align-middle'],
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn', 'header' => '#'],

                        [
                                'attribute' => 'id',
                                'label' => 'ID',
                                'enableSorting' => false,
                                'headerOptions' => ['style' => 'width:80px;'],
                        ],
                        [
                                'label' => 'Paciente',
                                'value' => fn($model) => $model->userprofile->nome ?? '-',
                        ],
                        [
                                'label' => 'Triagem',
                                'value' => fn($model) => $model->triagem->id ?? '-',
                        ],
                        [
                                'label' => 'Prescrição',
                                'value' => function ($model) {
                                    return count($model->prescricoes) > 0
                                            ? count($model->prescricoes)
                                            : '-';
                                }
                        ],
                        [
                                'label' => 'Estado',
                                'value' => 'estado',
                        ],
                        [
                                'attribute' => 'data_consulta',
                                'label' => 'Data da Consulta',
                                'enableSorting' => false,
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                                'headerOptions' => ['style' => 'min-width:160px; text-align:center;'],
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],
                        [
                                'label' => 'Consulta',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->estado === 'Encerrada') {
                                        return "<span class='badge bg-secondary'>Encerrada</span>";
                                    }

                                    return Html::a(
                                            '<i class="bi bi-x-octagon-fill"></i> Encerrar Consulta',
                                            ['consulta/encerrar', 'id' => $model->id],
                                            [
                                                    'class' => 'btn btn-danger btn-sm w-100',
                                                    'data-confirm' => 'Tens a certeza que queres encerrar esta consulta?',
                                                    'data-method' => 'post',
                                            ]
                                    );
                                },
                                'contentOptions' => ['style' => 'vertical-align:middle; text-align:center; min-width:170px;'],
                        ],
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update}',
                                'contentOptions' => ['style' => 'white-space:nowrap; text-align:center; vertical-align:middle;'],
                                'buttons' => [
                                        'view' => fn($url) => Html::a('<i class="bi bi-eye"></i>', $url, [
                                                'class' => 'btn-action btn-view', 'title' => 'Ver'
                                        ]),
                                        'update' => fn($url) => Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                'class' => 'btn-action btn-edit', 'title' => 'Editar'
                                        ]),
                                ],
                        ],
                ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
