<?php

use yii\helpers\Html;

$this->title = 'Nova Prescrição';
$this->params['breadcrumbs'][] = ['label' => 'Prescrições', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="container py-3">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>