<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Triagems', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="triagem-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'motivoconsulta',
            'queixaprincipal:ntext',
            'descricaosintomas:ntext',
            'iniciosintomas',
            'intensidadedor',
            'alergias:ntext',
            'medicacao:ntext',
            'motivo:ntext',
            'datatriagem',
            'userprofile_id',
            'pulseira_id',
        ],
    ]) ?>

</div>
