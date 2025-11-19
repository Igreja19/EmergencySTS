<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap5\ActiveForm;

$this->title = 'Medicamentos';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/medicamento/index.css');


?>

<div class="medicamento-index">

    <h3 class="page-title">
        <i class="bi bi-capsule"></i> Medicamentos
    </h3>

    <!-- =========================== -->
    <!--      BARRA DE PESQUISA      -->
    <!-- =========================== -->
    <div class="search-box">

        <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['data-pjax' => 1]
        ]); ?>

        <div class="row g-2 align-items-center">

            <div class="col-md-10">
                <?= Html::activeTextInput($searchModel, 'nome', [
                        'class' => 'form-control form-control-lg rounded-pill',
                        'placeholder' => 'Pesquisar medicamento por nome...',
                        'style' => 'padding-left:40px;'
                ]) ?>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <?= Html::submitButton('<i class="bi bi-search"></i> Procurar', [
                        'class' => 'btn btn-success w-100'
                ]) ?>

                <?= Html::a('<i class="bi bi-x-circle"></i> Limpar', ['index'], [
                        'class' => 'btn btn-outline-secondary w-100'
                ]) ?>
            </div>

        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <!-- BOTÃO CRIAR -->
    <p>
        <?= Html::a('<i class="bi bi-plus-circle"></i> Novo Medicamento', ['create'], [
                'class' => 'btn btn-success',
                'style' => 'border-radius:10px; font-weight:600;'
        ]) ?>
    </p>

    <!-- =========================== -->
    <!--           TABELA            -->
    <!-- =========================== -->
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
                            'headerOptions' => ['style' => 'width:80px;'],
                    ],

                    [
                            'attribute' => 'nome',
                            'label' => 'Nome',
                            'format' => 'text',
                    ],

                    [
                            'attribute' => 'dosagem',
                            'label' => 'Dosagem',
                    ],

                    [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => 'Ações',
                            'contentOptions' => ['style' => 'text-align:center; white-space:nowrap;'],
                            'template' => '{view} {update} {delete}',
                            'buttons' => [
                                    'view' => function ($url) {
                                        return Html::a('<i class="bi bi-eye"></i>', $url, [
                                                'class' => 'btn-action btn-view',
                                                'title' => 'Ver',
                                        ]);
                                    },
                                    'update' => function ($url) {
                                        return Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                'class' => 'btn-action btn-edit',
                                                'title' => 'Editar',
                                        ]);
                                    },
                                    'delete' => function ($url) {
                                        return Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn-action btn-delete',
                                                'title' => 'Eliminar',
                                                'data-confirm' => 'Deseja eliminar este medicamento?',
                                                'data-method' => 'post',
                                        ]);
                                    },
                            ],
                    ],

            ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
