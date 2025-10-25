<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */

$this->title = 'Update Triagem: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Triagems', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="triagem-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
