<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */
/** @var array $triagensDropdown */

$this->title = 'Create Pulseira';
$this->params['breadcrumbs'][] = ['label' => 'Pulseiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pulseira-create">

    <?= $this->render('_form', [
            'model' => $model,
            'triagensDropdown' => $triagensDropdown, // ← OBRIGATÓRIO
    ]) ?>

</div>
