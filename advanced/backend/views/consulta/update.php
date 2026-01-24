<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Consulta $model */

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/consulta/update.css');

$this->title = 'Editar Consulta #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Consultas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="update-container">

    <h3 class="update-header mb-4">
        <i class="bi bi-pencil-square"></i>
        <?= Html::encode($this->title) ?>
    </h3>

    <?= $this->render('_form', [
            'model' => $model,
            'triagensDisponiveis' => $triagensDisponiveis ?? [],
    ]) ?>

</div>
