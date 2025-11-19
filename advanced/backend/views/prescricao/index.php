<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Prescrições';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/prescricao/index.css');

?>

<div class="prescricao-index">

    <!-- Título principal -->
    <h1 class="page-title"><?= Html::encode($this->title) ?></h1>

    <!-- Card superior com título e botão -->
    <div class="title-card mb-4">

        <h2 class="page-card-title mb-0">
            <i class="bi bi-journal-medical me-2"></i> Prescrições
        </h2>

        <?= Html::a(
                '<i class="bi bi-plus-circle me-1"></i> Nova Prescrição',
                ['create'],
                ['class' => 'btn-new']
        ) ?>

    </div>

    <!-- Card da tabela -->
    <div class="card-table">

        <!-- Barra de pesquisa -->
        <div class="mb-3 search-wrapper">
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
                                'label' => 'ID',
                                'contentOptions' => ['style' => 'font-weight:700;color:#198754;'],
                        ],

                        [
                                'attribute' => 'observacoes',
                                'label' => 'Observações',
                                'format' => 'ntext',
                        ],

                        [
                                'attribute' => 'dataprescricao',
                                'label' => 'Data da Prescrição',
                                'format' => ['date', 'php:d/m/Y H:i'],
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],

                        [
                                'attribute' => 'consulta_id',
                                'label' => 'Consulta',
                                'value' => fn($m) => "Consulta #{$m->consulta_id}",
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],

                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['style' => 'text-align:center;'],

                                'urlCreator' => function ($action, $model) {
                                    return \yii\helpers\Url::to([$action, 'id' => $model->id]);
                                },

                                'buttons' => [
                                        'view' => fn($url) => Html::a('<i class="bi bi-eye"></i>', $url, [
                                                'class' => 'btn-action btn-view'
                                        ]),

                                        'update' => fn($url) => Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                'class' => 'btn-action btn-edit'
                                        ]),

                                        'delete' => fn($url) => Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn-action btn-delete',
                                                'data-confirm' => 'Tem certeza que deseja eliminar esta prescrição?',
                                                'data-method' => 'post',
                                        ]),
                                ],
                        ],

                ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>
</div>
