<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\TriagemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Triagens';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
.triagem-index {
  max-width: 1200px;
  margin: 0 auto;
}

.triagem-index h1 {
  color: #198754;
  font-weight: 700;
  margin-bottom: 25px;
  text-align: center;
}

.card-table {
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  border: none;
  background: #fff;
  padding: 25px;
}

.table-modern {
  border-radius: 14px;
  overflow: hidden;
}

/* Cabeçalho verde */
.table-modern thead tr {
  background: linear-gradient(90deg, #198754 0%, #28a745 100%);
  color: #fff;
  text-align: center;
}

/* Links no cabeçalho */
.table-modern th a {
  color: #fff !important;
  text-decoration: none;
  font-weight: 600;
}

/* 🔹 Texto preto nas células */
.table-modern td,
.table-modern th {
  color: #212529 !important;
}

.table-modern td {
  text-align: center;
  vertical-align: middle;
}

/* Badges de prioridade */
.badge-prio {
  padding: 6px 10px;
  border-radius: 8px;
  font-weight: 600;
  color: #fff;
}
.badge-Vermelho { background-color: #dc3545; }
.badge-Laranja  { background-color: #fd7e14; }
.badge-Amarelo  { background-color: #ffc107; color:#000; }
.badge-Verde    { background-color: #198754; }
.badge-Azul     { background-color: #0d6efd; }

/* Botões de ação */
.btn-action {
  border-radius: 10px;
  padding: 6px 10px;
  margin: 0 2px;
  font-size: 14px;
}
.btn-view { background:#0d6efd; color:#fff; }
.btn-edit { background:#198754; color:#fff; }
.btn-delete { background:#dc3545; color:#fff; }
.btn-action:hover { opacity: .85; }

/* Botão Nova Triagem */
.btn-new {
  background: linear-gradient(90deg, #198754 0%, #28a745 100%);
  color: #fff;
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(25,135,84,0.3);
  transition: 0.2s;
}
.btn-new:hover {
  opacity: .9;
  transform: translateY(-2px);
}
');
?>

<div class="triagem-index">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mb-0"><i class="bi bi-activity me-2"></i><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Nova Triagem', ['create'], ['class' => 'btn btn-new']) ?>
    </div>

    <div class="card-table">
        <div class="mb-3">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'tableOptions' => ['class' => 'table table-striped table-modern align-middle'],
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn', 'header' => '#'],

                        [
                                'attribute' => 'id',
                                'label' => 'ID',
                                'headerOptions' => ['style' => 'width:80px;'],
                        ],
                        [
                                'label' => 'Código da Pulseira',
                                'value' => fn($model) => $model->pulseira->codigo ?? '-',
                        ],
                        [
                                'label' => 'Paciente',
                                'value' => fn($model) => $model->userprofile->nome ?? '-',
                        ],
                        [
                                'attribute' => 'motivoconsulta',
                                'label' => 'Motivo da Consulta',
                        ],
                        [
                                'attribute' => 'datatriagem',
                                'label' => 'Data da Triagem',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                                'headerOptions' => ['style' => 'min-width:160px;'],
                        ],
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'contentOptions' => ['style' => 'white-space:nowrap; text-align:center;'],
                                'buttons' => [
                                        'view' => fn($url) => Html::a('<i class="bi bi-eye"></i>', $url, [
                                                'class' => 'btn-action btn-view',
                                                'title' => 'Ver'
                                        ]),
                                        'update' => fn($url) => Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                'class' => 'btn-action btn-edit',
                                                'title' => 'Editar'
                                        ]),
                                        'delete' => fn($url) => Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn-action btn-delete',
                                                'title' => 'Eliminar',
                                                'data-confirm' => 'Tens a certeza que queres eliminar esta triagem?',
                                                'data-method' => 'post',
                                        ]),
                                ],
                        ],
                ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
