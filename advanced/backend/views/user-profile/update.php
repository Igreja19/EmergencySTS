<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */

$this->title = 'Editar Perfil: ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Perfis de Utilizador', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>

<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-success text-white d-flex align-items-center justify-content-between rounded-top">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?>
            </h5>
        </div>

        <div class="card-body bg-light p-4">
            <?= $this->render('_form', [
                    'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
