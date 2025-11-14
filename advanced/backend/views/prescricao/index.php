<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Prescrições';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile.css');

?>

<div class="prescricao-index">

    <!-- Título + botão igual ao dos utilizadores -->
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

        <!-- 🔍 Barra de pesquisa igual à dos utilizadores -->
        <div class="mb-3">
            <?= $this->render('_search', ['model' => $searchModel]) ?>
        </div>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null, // igual aos utilizadores
                'tableOptions' => ['class' => 'table table-striped table-modern align-middle'],

                'columns' => [
                        ['class' => 'yii\grid\SerialColumn', 'header' => '#'],

                        [
                                'attribute' => 'id',
                                'contentOptions' => ['style' => 'font-weight:600;'],
                        ],

                        [
                                'attribute' => 'observacoes',
                                'format' => 'ntext',
                                'label' => 'Observações',
                        ],

                        [
                                'attribute' => 'dataprescricao',
                                'label' => 'Data',
                                'format' => ['date', 'php:d/m/Y H:i'],
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],

                        [
                                'attribute' => 'consulta_id',
                                'label' => 'Consulta',
                                'value' => fn($m) => "Consulta #" . $m->consulta_id,
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],

                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['style' => 'text-align:center;'],

                                'buttons' => [
                                        'view' => fn($url) =>
                                        Html::a('<i class="bi bi-eye"></i>', $url, [
                                                'class' => 'btn-action btn-view'
                                        ]),
                                        'update' => fn($url) =>
                                        Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                'class' => 'btn-action btn-edit'
                                        ]),
                                        'delete' => fn($url) =>
                                        Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn-action btn-delete',
                                                'data-confirm' => 'Tem a certeza que deseja eliminar esta prescrição?',
                                                'data-method' => 'post',
                                        ]),
                                ],
                        ],
                ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>
</div>
