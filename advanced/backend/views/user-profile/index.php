<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Utilizadores';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile/index.css');
?>

<div class="userprofile-index">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mb-0"><i class="bi bi-people-fill me-2"></i><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-person-plus me-1"></i> Novo Utilizador', ['create'], ['class' => 'btn btn-new']) ?>
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
                        'id',
                        'nome',
                        'email:email',
                        'telefone',
                        [
                                'attribute' => 'genero',
                                'value' => fn($m) => match ($m->genero) {
                                    'M' => 'Masculino',
                                    'F' => 'Feminino',
                                    'O' => 'Outro',
                                    default => '—',
                                },
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],
                        [
                                'attribute' => 'datanascimento',
                                'format' => ['date', 'php:d/m/Y'],
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],
                        [
                                'label' => 'Função / Role',
                                'value' => function ($model) {
                                    $roleOptions = Yii::$app->authManager->getRolesByUser($model->user_id);
                                    return !empty($roleOptions) ? ucfirst(array_keys($roleOptions)[0]) : '—';
                                },
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],
                        'nif',
                        'sns',
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['style' => 'text-align:center;'],
                                'buttons' => [
                                        'view' => fn($url) => Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn-action btn-view']),
                                        'update' => fn($url) => Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn-action btn-edit']),
                                        'delete' => fn($url) => Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn-action btn-delete',
                                                'data-confirm' => 'Tens a certeza que queres eliminar este utilizador?',
                                                'data-method' => 'post',
                                        ]),
                                ],
                        ],
                        [
                                'attribute' => 'estado',
                                'label' => 'Estado',
                                'format' => 'raw',
                                'value' => fn($model) => $model->getEstadoBadge(),
                                'contentOptions' => ['style' => 'text-align:center; width:120px'],
                        ]
                ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
