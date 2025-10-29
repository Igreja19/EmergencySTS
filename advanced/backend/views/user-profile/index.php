<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\UserProfileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Utilizadores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paciente-index">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="m-0"><?= Html::encode($this->title) ?></h1>
        <div class="btn-group">
            <?= Html::a('<i class="fas fa-plus mr-1"></i> Novo', ['create'], ['class'=>'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-sync-alt"></i>', ['index'], ['class'=>'btn btn-outline-secondary', 'title'=>'Atualizar']) ?>
        </div>
    </div>

    <?php Pjax::begin(['timeout'=>8000, 'enablePushState'=>false]) ?>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <?= $this->render('_search', ['model' => $searchModel]) ?>
            </div>

            <div class="table-responsive-lg">
                <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => null, // usamos _search
                        'tableOptions' => ['class' => 'table table-hover align-middle'],
                        'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{summary}{pager}</div>",
                        'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                'nome',
                                [
                                        'attribute' => 'nif',
                                        'contentOptions'=>['style'=>'white-space:nowrap;']
                                ],
                                [
                                        'attribute' => 'datanascimento',
                                        'format' => ['date','php:d/m/Y'],
                                        'label' => 'Nascimento',
                                        'contentOptions'=>['style'=>'white-space:nowrap;']
                                ],
                                [
                                        'attribute' => 'genero',
                                        'filter' => false,
                                ],
                                [
                                        'attribute' => 'telefone',
                                        'contentOptions'=>['style'=>'white-space:nowrap;']
                                ],
                                'email:email',

                                [
                                        'class' => 'yii\grid\ActionColumn',
                                        'contentOptions'=>['style'=>'white-space:nowrap;'],
                                        'template' => '{view} {update} {delete}',
                                        'buttons' => [
                                                'view' => fn($url,$model) => Html::a('<i class="fas fa-eye"></i>', $url, ['class'=>'btn btn-sm btn-outline-secondary','title'=>'Ver']),
                                                'update' => fn($url,$model) => Html::a('<i class="fas fa-edit"></i>', $url, ['class'=>'btn btn-sm btn-outline-primary','title'=>'Editar']),
                                                'delete' => fn($url,$model) => Html::a('<i class="fas fa-trash"></i>', $url, [
                                                        'class'=>'btn btn-sm btn-outline-danger','title'=>'Eliminar',
                                                        'data-confirm'=>'Eliminar este registo?','data-method'=>'post'
                                                ]),
                                        ],
                                ],
                        ],
                ]) ?>
            </div>
        </div>
    </div>

    <?php Pjax::end() ?>
</div>
