<?php
use yii\helpers\Html;

/** @var common\models\Paciente $model */

$this->title = $model->isNewRecord ? 'Novo Paciente' : 'Atualizar Paciente';
$this->params['breadcrumbs'][] = ['label' => 'Pacientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paciente-create">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
