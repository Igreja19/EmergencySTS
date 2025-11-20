<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */

$this->title = 'Editar Pulseira';
$this->params['breadcrumbs'][] = ['label' => 'Pulseiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Ver Pulseira #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/pulseira/update.css');
?>

<div class="pulseira-update">
    <h1><i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?></h1>

    <div class="pulseira-form">
        <?= $this->render('_form', [
                'model' => $model,
        ]) ?>
    </div>

    <div class="text-center mt-3">
        <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-back']) ?>
    </div>
</div>
