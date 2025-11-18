<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Triagens';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/triagem/index.css');
?>

<div class="triagem-index">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mb-0"><i class="bi bi-activity me-2"></i><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Nova Triagem', ['create'], ['class' => 'btn btn-new']) ?>
    </div>

    <div class="card-table">
        <div class="mb-3">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-modern align-middle'],
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn', 'header' => '#'],
                        'id',
                        [
                                'label' => 'Código da Pulseira',
                                'value' => fn($m) => $m->pulseira->codigo ?? '-',
                        ],
                        [
                                'label' => 'Paciente',
                                'value' => fn($m) => $m->userprofile->nome ?? '-',
                        ],
                        'motivoconsulta',
                        [
                                'attribute' => 'datatriagem',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                        ],
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['class' => 'acoes'],
                                'buttons' => [
                                        'view' => fn($url) => Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn-action btn-view']),
                                        'update' => fn($url) => Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn-action btn-edit']),
                                        'delete' => fn($url) => Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn-action btn-delete',
                                                'data-confirm' => 'Tens a certeza que queres eliminar esta triagem?',
                                                'data-method' => 'post',
                                        ]),
                                ],
                        ],
                ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
