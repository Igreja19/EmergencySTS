<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\UserProfileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Utilizadores';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile.css');
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

                        [
                                'attribute' => 'id',
                                'label' => 'ID',
                                'headerOptions' => ['style' => 'width:80px;'],
                        ],
                        [
                                'attribute' => 'nome',
                                'label' => 'Nome',
                        ],
                        [
                                'attribute' => 'email',
                                'label' => 'Email',
                                'format' => 'email',
                        ],
                        [
                                'attribute' => 'telefone',
                                'label' => 'Telefone',
                        ],
                        [
                                'attribute' => 'genero',
                                'label' => 'Género',
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
                                'label' => 'Nascimento',
                                'format' => ['date', 'php:d/m/Y'],
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],
                        [
                                'label' => 'Função / Role',
                                'value' => function ($model) {
                                    $roles = Yii::$app->authManager->getRolesByUser($model->user_id);
                                    if (!empty($roles)) {
                                        return ucfirst(array_keys($roles)[0]);
                                    }
                                    return '—';
                                },
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],
                        [
                                'attribute' => 'nif',
                                'label' => 'NIF',
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],
                        [
                                'attribute' => 'sns',
                                'label' => 'SNS',
                                'contentOptions' => ['style' => 'text-align:center;'],
                        ],

                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['style' => 'white-space:nowrap; text-align:center;'],
                                'buttons' => [
                                        'view' => fn($url) => Html::a('<i class="bi bi-eye"></i>', $url, [
                                                'class' => 'btn-action btn-view',
                                                'title' => 'Ver'
                                        ]),
                                        'update' => fn($url) => Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                'class' => 'btn-action btn-edit',
                                                'title' => 'Editar'
                                        ]),
                                        'delete' => fn($url) => Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn-action btn-delete',
                                                'title' => 'Eliminar',
                                                'data-confirm' => 'Tens a certeza que queres eliminar este utilizador?',
                                                'data-method' => 'post',
                                        ]),
                                ],
                        ],
                ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
