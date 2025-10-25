<?php

use common\models\Triagem;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\TriagemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Triagems';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="triagem-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Triagem', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nomecompleto',
            'datanascimento',
            'sns',
            'telefone',
            //'motivoconsulta',
            //'queixaprincipal:ntext',
            //'descricaosintomas:ntext',
            //'iniciosintomas',
            //'intensidadedor',
            //'condicoes:ntext',
            //'alergias:ntext',
            //'medicacao:ntext',
            //'motivo:ntext',
            //'prioridadeatribuida',
            //'datatriagem',
            //'discriminacaoprincipal',
            //'paciente_id',
            //'utilizador_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Triagem $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
