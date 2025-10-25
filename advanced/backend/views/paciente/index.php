<?php

use common\models\Paciente;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var \common\models\PacienteSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Pacientes';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    /* === Layout geral === */
    .container-fluid {
        background: #fff;
        border-radius: 12px;
        padding: 25px 30px;
        margin-top: 25px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    /* === Título === */
    .paciente-index h1 {
        color: #198754;
        font-weight: 600;
        margin-bottom: 25px;
    }

    /* === Botão criar === */
    .btn-success {
        background-color: #198754;
        border: none;
        font-weight: 500;
        padding: 8px 18px;
        border-radius: 6px;
    }
    .btn-success:hover {
        background-color: #157347;
    }

    /* === Tabela moderna === */
    .table-container {
        border-radius: 12px;
        overflow-x: auto; /* permite scroll se necessário */
        overflow-y: visible;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 10px;
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }

    /* Cabeçalho */
    .table thead tr {
        background-color: #198754;
        color: #fff;
        font-weight: 500;
    }

    .table th {
        border: none;
        padding: 12px 10px;
        text-align: center;
        vertical-align: middle;
    }

    .table th a {
        color: #fff !important;
        text-decoration: none;
    }
    .table th a:hover {
        text-decoration: underline;
    }

    /* Linhas */
    .table tbody td {
        border-top: 1px solid #dee2e6;
        vertical-align: middle;
        text-align: center;
        padding: 10px 8px;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .table tbody tr:hover {
        background-color: #e9f7ef;
        transition: 0.3s;
    }

    /* Canto arredondado */
    .table thead tr:first-child th:first-child {
        border-top-left-radius: 12px;
    }
    .table thead tr:first-child th:last-child {
        border-top-right-radius: 12px;
    }
    .table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 12px;
    }
    .table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 12px;
    }

    /* Responsividade */
    @media (max-width: 992px) {
        .table th, .table td {
            font-size: 14px;
            white-space: nowrap;
        }
    }
</style>

<div class="container-fluid paciente-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Criar Paciente', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="table-container">
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-hover table-borderless align-middle'],
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'id',
                        'nomecompleto',
                        [
                                'attribute' => 'datanascimento',
                                'format' => ['date', 'php:d/m/Y'],
                        ],
                        'sns',
                        'telefone',
                        'email:email',
                        'morada',
                        'genero',
                        'nif',
                        'observacoes:ntext',
                        [
                                'class' => ActionColumn::class,
                                'header' => 'Ações',
                                'contentOptions' => ['style' => 'min-width:90px; text-align:center;'],
                                'urlCreator' => function ($action, Paciente $model, $key, $index, $column) {
                                    return Url::toRoute([$action, 'id' => $model->id]);
                                },
                        ],
                ],
        ]); ?>
    </div>
</div>
