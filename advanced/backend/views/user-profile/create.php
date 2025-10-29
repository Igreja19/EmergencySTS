<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Userprofile $model */

$this->title = 'Create Utilizador';
$this->params['breadcrumbs'][] = ['label' => 'Pacientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paciente-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
