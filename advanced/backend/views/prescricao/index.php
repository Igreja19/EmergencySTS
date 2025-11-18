<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Prescrições';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/prescricao/index.css');

?>

<div class="prescricao-index">

    <!-- Título + botão -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mb-0">
            <i class="bi bi-journal-medical me-2"></i><?= Html::encode($this->title) ?>
        </h1>

        <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Nova Prescrição', ['create'], [
                'class' => 'btn btn-new'
        ]) ?>
    </div>

    <!-- CARD PRINCIPAL -->
    <div class="card-table">

        <!-- 🔍 Barra de pesquisa -->
        <div class="mb-3">
            <?= $this->render('_search', ['model' => $searchModel]) ?>
        </div>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => null,
                'tableOptions' => ['class' => 'table table-striped table-modern align-middle'],

                'columns' => [
                        ['class' => 'yii\grid\SerialColumn', 'header' => '#'],

                        [
                                'attribute' => 'id',
                                'contentOptions' => ['class' => 'id'],
                        ],

                        [
                                'attribute' => 'observacoes',
                                'label' => 'Observações',
                                'format' => 'ntext'
                        ],

                        [
                                'attribute' => 'dataprescricao',
                                'label' => 'Data',
                                'format' => ['date', 'php:d/m/Y H:i'],
                                'contentOptions' => ['class' => 'dataprescricao'],
                        ],

                        [
                                'attribute' => 'consulta_id',
                                'label' => 'Consulta',
                                'value' => fn($m) => "Consulta #" . $m->consulta_id,
                                'contentOptions' => ['class' => 'consulta-id'],
                        ],

                    /* ================================
                     *      🔥 ACTION COLUMN FIXADA
                     * ================================ */
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['class' => 'acoes'],

                            // 🔥 Corrige completamente o erro Missing parameter: id
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return \yii\helpers\Url::to([$action, 'id' => $model->id]);
                                },

                                'buttons' => [
                                        'view' => function ($url) {
                                            return Html::a('<i class="bi bi-eye"></i>', $url, [
                                                    'class' => 'btn-action btn-view'
                                            ]);
                                        },

                                        'update' => function ($url) {
                                            return Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                    'class' => 'btn-action btn-edit'
                                            ]);
                                        },

                                        'delete' => function ($url) {
                                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                                    'class' => 'btn-action btn-delete',
                                                    'data-confirm' => 'Tem a certeza que deseja eliminar esta prescrição?',
                                                    'data-method' => 'post',
                                            ]);
                                        }
                                ],
                        ],
                ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>
</div>
