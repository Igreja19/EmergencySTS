<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/triagem/update.css');

$this->title = 'Editar Triagem';
$this->params['breadcrumbs'][] = ['label' => 'Triagens', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Ver Triagem #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';

?>

<div class="triagem-update">
    <h1><i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
    ]) ?>

    <?php $fromPulseira = Yii::$app->request->get('fromPulseira'); ?>

    <div class="text-center mt-3 butao">

        <?= Html::a(
                '<i class="bi bi-arrow-left-circle me-1"></i> Voltar',
                $fromPulseira
                        ? ['triagem/view', 'id' => $model->id, 'pulseira_id' => $fromPulseira]
                        : ['view', 'id' => $model->id],
                ['class' => 'btn btn-voltar']
        ) ?>

    </div>
</div>
