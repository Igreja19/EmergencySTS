<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Medicamento $model */

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/medicamento/view.css');

$this->title = $model->nome . ' ' . $model->dosagem;
$this->params['breadcrumbs'][] = ['label' => 'Medicamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="medicamento-view">

    <div class="view-box mb-3">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h3 class="title-header mb-0">
                <i class="bi bi-capsule-pill"></i>
                <?= Html::encode($this->title) ?>
            </h3>

            <div class="d-flex align-items-center gap-2">
                <?= Html::a('<i class="bi bi-arrow-left"></i>', ['index'], ['class' => 'btn-back me-2', 'title' => 'Voltar']) ?>

                <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                        'class' => 'btn-circle btn-edit',
                        'title' => 'Editar',
                ]) ?>

                <?= Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
                        'class' => 'btn-circle btn-delete',
                        'title' => 'Eliminar',
                        'data-confirm' => 'Tens a certeza que queres eliminar este medicamento?',
                        'data-method' => 'post',
                ]) ?>
            </div>
        </div>

        <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-bordered table-detail'],
                'attributes' => [
                        'id',
                        'nome',
                        'dosagem',
                        [
                                'attribute' => 'indicacao',
                                'format' => 'ntext',
                        ],
                        [
                                'attribute' => 'quantidade_diaria',
                                'label' => 'Quantidade diária',
                        ],
                        [
                                'attribute' => 'duracao_tratamento',
                                'label' => 'Duração do tratamento',
                        ],
                ],
        ]) ?>

    </div>

</div>
