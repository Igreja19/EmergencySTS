<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\PrescricaoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/prescricao/index.css');

$this->title = 'Prescrições';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="prescricao-index">


    <!-- HEADER DA SECÇÃO -->
    <div class="section-header card shadow-sm mb-4 p-0" style="border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center p-3"
             style="background-color: #1f9d55; border-radius: 12px 12px 0 0;">
            <h4 class="text-white m-0">
                <i class="bi bi-file-text-fill me-2"></i> Prescrições
            </h4>

            <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Nova Prescrição',
                    ['create'],
                    ['class' => 'btn btn-light text-success fw-bold']
            ) ?>
        </div>

        <div class="p-3">
            <!-- CAMPO DE PESQUISA -->
            <form method="get" class="d-flex align-items-center gap-2">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="date"
                           name="dataFiltro"
                           class="form-control border-start-0"
                           placeholder="dd/mm/aaaa"
                           value="<?= Yii::$app->request->get('dataFiltro') ?>">
                </div>

                <button class="btn btn-success">
                    <i class="bi bi-search"></i> Procurar
                </button>

                <a href="<?= Yii::$app->urlManager->createUrl(['prescricao/index']) ?>"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Limpar
                </a>
            </form>
        </div>
    </div>

    <!-- TABELA -->
    <div class="card shadow-sm p-3" style="border-radius: 12px;">

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
                'dataProvider'  => $dataProvider,
                'filterModel'   => null,
                'summary'       => '<small>Mostrando <b>{count}</b> de <b>{totalCount}</b> itens.</small>',
                'tableOptions'  => ['class' => 'table table-striped align-middle'],
                'headerRowOptions' => ['class' => 'table-light'],

                'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                                'attribute' => 'id',
                                'contentOptions' => ['style' => 'width:50px; font-weight:bold; color:#1f9d55;']
                        ],

                        [
                                'attribute' => 'observacoes',
                                'label' => 'Observações',
                        ],

                        [
                                'attribute' => 'dataprescricao',
                                'label' => 'Data da Prescrição',
                                'format' => ['date', 'php:d/m/Y H:i']
                        ],

                        [
                                'attribute' => 'consulta_id',
                                'label' => 'Consulta',
                                'value' => function ($model) {
                                    return $model->consulta ? 'Consulta #' . $model->consulta_id : '-';
                                }
                        ],

                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'contentOptions' => ['style' => 'text-align:center; width:140px;'],
                                'template' => '{view} {update} {delete}',

                                'buttons' => [
                                        'view' => function ($url) {
                                            return Html::a('<i class="bi bi-eye"></i>', $url, [
                                                    'class' => 'btn btn-primary btn-sm me-1',
                                                    'title' => 'Ver'
                                            ]);
                                        },

                                        'update' => function ($url) {
                                            return Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                    'class' => 'btn btn-success btn-sm me-1',
                                                    'title' => 'Editar'
                                            ]);
                                        },

                                        'delete' => function ($url) {
                                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'title' => 'Eliminar',
                                                    'data-confirm' => 'Tem a certeza que deseja eliminar esta prescrição?',
                                                    'data-method' => 'post'
                                            ]);
                                        },
                                ],
                        ],
                ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>

</div>