<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Userprofile $model */

$this->title = 'Criar Novo Perfil';
$this->params['breadcrumbs'][] = ['label' => 'Perfis de Utilizador', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-success text-white d-flex align-items-center justify-content-between rounded-top">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-person-plus-fill me-2"></i><?= Html::encode($this->title) ?>
            </h5>
        </div>

        <div class="card-body bg-light p-4">
            <?= $this->render('_form', [
                    'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
