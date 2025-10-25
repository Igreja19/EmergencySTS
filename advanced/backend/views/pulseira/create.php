<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */

$this->title = 'Create Pulseira';
$this->params['breadcrumbs'][] = ['label' => 'Pulseiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pulseira-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
