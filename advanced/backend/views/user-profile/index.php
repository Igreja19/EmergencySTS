<?php

use common\models\Userprofile;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\UserProfileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Userprofiles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="userprofile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Userprofile', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
            'email:email',
            'nif',
            'sns',
            //'datanascimento',
            //'genero',
            //'telefone',
            //'consulta_id',
            //'triagem_id',
            //'user_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Userprofile $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
