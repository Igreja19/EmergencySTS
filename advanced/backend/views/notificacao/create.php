<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Notificacao $model */

$this->title = 'Create Notificacao';
$this->params['breadcrumbs'][] = ['label' => 'Notificacaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notificacao-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
