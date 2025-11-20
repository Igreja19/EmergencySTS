<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Consulta $model */

$this->title = 'Editar Consulta #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Consultas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
.update-header {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 28px;
    font-weight: 700;
    color: #198754;
}
.update-container {
    max-width: 950px;
    margin: 0 auto;
}
");

?>

<div class="update-container">

    <!-- TÍTULO LARGO IGUAL AO EDITAR PERFIL -->
    <h3 class="update-header mb-4">
        <i class="bi bi-pencil-square"></i>
        <?= Html::encode($this->title) ?>
    </h3>

    <!-- FORMULÁRIO MODERNO (vem de _form.php) -->
    <?= $this->render('_form', [
            'model' => $model,
            'triagensDisponiveis' => $triagensDisponiveis ?? [],
    ]) ?>

</div>
