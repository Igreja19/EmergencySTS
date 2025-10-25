<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Observacao $model */

$this->title = 'Create Observacao';
$this->params['breadcrumbs'][] = ['label' => 'Observacaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="observacao-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
