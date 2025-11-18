<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Medicamento $model */

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/medicamento/update.css');

$this->title = 'Editar Medicamento: ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Medicamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';

?>

<div class="medicamento-update">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="page-title mb-0">
            <i class="bi bi-capsule-pill"></i>
            <?= Html::encode($this->title) ?>
        </h1>

        <?= Html::a('<i class="bi bi-arrow-left"></i> Voltar', ['view', 'id' => $model->id], ['class' => 'btn-back']) ?>
    </div>

    <?= $this->render('_form', [
            'model' => $model,
    ]) ?>

</div>
