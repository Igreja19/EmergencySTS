<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Perfil do Utilizador';
\yii\web\YiiAsset::register($this);
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge bg-light text-success px-3 py-2 fw-semibold">EmergencySTS</span>
        <h3 class="fw-bold text-success mt-3"><i class="bi bi-person-circle me-2"></i>Perfil do Utilizador</h3>
        <p class="text-muted">Visualize e atualize as informações associadas à sua conta.</p>
    </div>

    <div class="mx-auto card shadow-sm border-0 rounded-4 p-4" style="max-width: 850px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-success mb-0"><i class="bi bi-person-lines-fill me-2"></i>Dados do Perfil</h5>
            <div>
                <?= Html::a('<i class="bi bi-pencil-square me-1"></i> Atualizar', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-success fw-semibold shadow-sm'
                ]) ?>
                <?= Html::a('<i class="bi bi-trash3 me-1"></i> Eliminar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-outline-danger fw-semibold shadow-sm',
                        'data' => [
                                'confirm' => 'Tem a certeza que deseja eliminar este perfil?',
                                'method' => 'post',
                        ],
                ]) ?>
            </div>
        </div>

        <!-- PERFIL DETALHADO -->
        <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-borderless align-middle'],
                'attributes' => [
                        [
                                'attribute' => 'nomecompleto',
                                'label' => '<i class="bi bi-person-fill me-2"></i> Nome Completo',
                                'format' => 'raw',
                        ],
                        [
                                'attribute' => 'email',
                                'label' => '<i class="bi bi-envelope-fill me-2"></i> Email',
                                'format' => 'email',
                        ],
                        [
                                'attribute' => 'telefone',
                                'label' => '<i class="bi bi-telephone-fill me-2"></i> Telefone',
                                'format' => 'raw',
                        ],
                        [
                                'attribute' => 'nif',
                                'label' => '<i class="bi bi-credit-card-2-front-fill me-2"></i> NIF',
                        ],
                        [
                                'attribute' => 'sns',
                                'label' => '<i class="bi bi-hospital-fill me-2"></i> Nº SNS',
                        ],
                        [
                                'attribute' => 'genero',
                                'label' => '<i class="bi bi-gender-ambiguous me-2"></i> Género',
                        ],
                        [
                                'attribute' => 'datanascimento',
                                'label' => '<i class="bi bi-calendar3 me-2"></i> Data de Nascimento',
                        ],
                        [
                                'attribute' => 'morada',
                                'label' => '<i class="bi bi-geo-alt-fill me-2"></i> Morada',
                        ],
                        [
                                'attribute' => 'ativo',
                                'label' => '<i class="bi bi-person-check-fill me-2"></i> Estado da Conta',
                                'value' => $model->ativo ? 'Ativo' : 'Inativo',
                                'format' => 'raw',
                        ],
                ],
        ]) ?>
    </div>
</div>

<!-- 🔹 CSS -->
<style>
    body {
        background: linear-gradient(180deg, #f8fff9 0%, #eef8ef 100%);
    }

    .card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 18px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.1);
    }

    .table th, .table td {
        padding: 12px 16px !important;
        border-top: none !important;
    }

    .table tr:nth-child(even) {
        background-color: #f9fdf9;
    }

    .table td {
        color: #212529;
    }

    .table th {
        color: #198754;
        width: 35%;
        font-weight: 600;
        background: none !important;
    }

    .btn-success {
        background-color: #198754 !important;
        border: none;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background-color: #16a34a !important;
        box-shadow: 0 4px 15px rgba(22, 163, 74, 0.4);
        transform: translateY(-2px);
    }

    .btn-outline-danger {
        border-radius: 10px;
    }
</style>
