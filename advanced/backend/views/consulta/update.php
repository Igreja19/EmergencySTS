<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Consulta $model */

$this->title = $this->context->action->id === 'create'
        ? 'Nova Consulta'
        : 'Atualizar Consulta';

$this->params['breadcrumbs'][] = ['label' => 'Consultas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/consulta/update.css');
?>
<div class="consulta-create">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mb-0 text-success"><i class="bi bi-clipboard-plus me-2"></i><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?= $this->render('_form', ['model' => $model]) ?>
</div>
