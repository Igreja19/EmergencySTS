<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */

$this->title = 'Editar Triagem';
$this->params['breadcrumbs'][] = ['label' => 'Triagens', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Ver Triagem #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';

$this->registerCss('
.triagem-update {
  max-width: 900px;
  margin: 0 auto;
}
.triagem-update h1 {
  color: #198754;
  font-weight: 700;
  margin-bottom: 25px;
  text-align: center;
}
.btn-back {
  background: #198754;
  color: #fff;
  border-radius: 10px;
  padding: 10px 20px;
  font-weight: 600;
  transition: .2s;
}
.btn-back:hover { opacity: .9; transform: translateY(-2px); }
');
?>

<div class="triagem-update">
    <h1><i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
    ]) ?>

    <div class="text-center mt-3">
        <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-back']) ?>
    </div>
</div>
