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
                                'headerOptions' => ['class' => 'id'],
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
                                'value' => fn($model) => $model->prescricao->id ?? '-',
                        ],
                        [
                                'attribute' => 'estado',
                                'label' => 'Estado',
                        ],
                        [
                                'attribute' => 'data_consulta',
                                'label' => 'Data da Consulta',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                                'headerOptions' => ['class' => 'data-consulta-header'],
                                'contentOptions' => ['class' => 'data-consulta-content'],
                        ],
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['class' => 'acoes'],
                                'buttons' => [
                                        'view' => fn($url) => Html::a('<i class="bi bi-eye"></i>', $url, [
                                                'class' => 'btn-action btn-view', 'title' => 'Ver'
                                        ]),
                                        'update' => fn($url) => Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                'class' => 'btn-action btn-edit', 'title' => 'Editar'
                                        ]),
                                        'delete' => fn($url) => Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn-action btn-delete', 'title' => 'Eliminar',
                                                'data-confirm' => 'Tens a certeza que queres eliminar esta consulta?',
                                                'data-method' => 'post',
                                        ]),
                                ],
                        ],
                ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
