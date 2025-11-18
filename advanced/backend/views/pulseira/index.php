<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Pulseiras';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
.page-title {
    font-size: 30px;
    font-weight: 700;
    color: #198754;
    display: flex;
    align-items: center;
    gap: 10px;
}
.page-title i { font-size: 32px; }

.card-box {
    background: #fff;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.table-modern {
    border-radius: 14px;
    overflow: hidden;
}

/* 🔥 Alinhar tudo ao centro (thead + tbody) */
.table-modern th, 
.table-modern td {
    text-align: center !important;
    vertical-align: middle !important;
}

.table-modern thead tr {
    background: #f0f2f5;
    font-weight: 700;
    color: #198754;
}

.table-modern tbody tr:hover {
    background: #f8f9fa;
}

.badge-prio {
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 600;
    color: #fff;
}

/* Cores Manchester */
.badge-Vermelho { background-color: #dc3545; }
.badge-Laranja  { background-color: #fd7e14; }
.badge-Amarelo  { background-color: #ffc107; color:#000; }
.badge-Verde    { background-color: #198754; }
.badge-Azul     { background-color: #0d6efd; }

.btn-action {
    padding: 6px 10px;
    border-radius: 10px;
    color: white;
}
.btn-view { background:#0d6efd; }
.btn-edit { background:#198754; }
.btn-delete { background:#dc3545; }

.btn-new {
    background: #198754;
    color:#fff;
    padding:10px 18px;
    border-radius:12px;
    font-weight:600;
}
.btn-new:hover { opacity:.9; }
');

?>

<div class="pulseira-index">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title"><i class="bi bi-upc-scan"></i> Pulseiras</h1>

        <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Nova Pulseira', ['create'], [
                'class' => 'btn-new'
        ]) ?>
    </div>

    <!-- SEARCH + TABLE CARD -->
    <div class="card-box">

        <!-- Filtros estilo Prescrições -->
        <div class="mb-3">
            <?= $this->render('_search', ['model' => $searchModel]) ?>
        </div>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-modern align-middle'],
                'columns' => [

                        ['class' => 'yii\grid\SerialColumn', 'header' => '#'],

                        [
                                'attribute' => 'id',
                                'label' => 'ID',
                                'headerOptions' => ['style' => 'width:70px;'],
                        ],

                        [
                                'attribute' => 'codigo',
                                'label' => 'Código da Pulseira',
                        ],

                        [
                                'attribute' => 'prioridade',
                                'label' => 'Prioridade',
                                'format' => 'raw',
                                'value' => function ($m) {
                                    if (!$m->prioridade) {
                                        return "<span class='text-secondary'>—</span>";
                                    }
                                    return "<span class='badge-prio badge-{$m->prioridade}'>{$m->prioridade}</span>";
                                }
                        ],

                        [
                                'attribute' => 'tempoentrada',
                                'label' => 'Entrada',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                        ],

                        [
                                'label' => 'Paciente',
                                'value' => fn($m) => $m->userprofile->nome ?? '—'
                        ],

                        [
                                'label' => 'Triagem',
                                'value' => fn($m) => $m->triagem->motivoconsulta ?? '—'
                        ],

                        [
                                'attribute' => 'status',
                                'value' => function ($m) {
                                    return match ($m->status) {
                                        'Em espera' => '⏳ A aguardar Atendimento',
                                        'Em atendimento' => '🩺 Em Atendimento',
                                        'Atendido' => '✅ Atendido',
                                        default => $m->status
                                    };
                                }
                        ],

                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['style' => 'text-align:center;'],
                                'buttons' => [
                                        'view' => fn($url) =>
                                        Html::a('<i class="bi bi-eye"></i>', $url, ['class'=>'btn-action btn-view']),

                                        'update' => fn($url) =>
                                        Html::a('<i class="bi bi-pencil"></i>', $url, ['class'=>'btn-action btn-edit']),

                                        'delete' => fn($url) =>
                                        Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class'=>'btn-action btn-delete',
                                                'data-confirm'=>'Tens a certeza?',
                                                'data-method'=>'post'
                                        ]),
                                ],
                        ],
                ],
        ]) ?>

        <?php Pjax::end(); ?>
    </div>
</div>
