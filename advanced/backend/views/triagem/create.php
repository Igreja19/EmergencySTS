<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */

$this->title = 'Create Triagem';
$this->params['breadcrumbs'][] = ['label' => 'Triagems', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="triagem-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
