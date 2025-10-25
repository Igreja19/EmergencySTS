<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Observacao $model */

$this->title = 'Update Observacao: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Observacaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="observacao-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
