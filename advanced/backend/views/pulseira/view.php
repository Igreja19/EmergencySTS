<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */

$this->title = 'Detalhes da Pulseira #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pulseiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// ====== CSS moderno ======
$this->registerCss('
.view-card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  padding: 30px;
  max-width: 900px;
  margin: 0 auto;
}
.view-card h3 {
  color: #198754;
  font-weight: 700;
  margin-bottom: 20px;
}
.badge-prio {
  padding: 8px 14px;
  border-radius: 8px;
  font-weight: 600;
  color: #fff;
}
.badge-Vermelho { background-color: #dc3545; }
.badge-Laranja  { background-color: #fd7e14; }
.badge-Amarelo  { background-color: #ffc107; color:#000; }
.badge-Verde    { background-color: #198754; }
.badge-Azul     { background-color: #0d6efd; }
.btn-back {
  background: linear-gradient(90deg, #198754 0%, #28a745 100%);
  color: #fff;
  font-weight: 600;
  border-radius: 12px;
  padding: 10px 20px;
  transition: .2s;
}
.btn-back:hover { opacity: .9; transform: translateY(-2px); }
.detail-view th {
  width: 220px;
  color: #198754;
  font-weight: 600;
}
.detail-view td {
  color: #333;
}
');
?>

<div class="pulseira-view">
    <div class="view-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h3><i class="bi bi-upc-scan me-2"></i><?= Html::encode($this->title) ?></h3>
            <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-back']) ?>
        </div>

        <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                        'id',
                        [
                                'attribute' => 'codigo',
                                'label' => 'Código da Pulseira',
                                'format' => 'text',
                        ],
                        [
                                'attribute' => 'prioridade',
                                'label' => 'Prioridade',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $cor = $model->prioridade ?? '-';
                                    return $cor ? "<span class='badge-prio badge-{$cor}'>{$cor}</span>" : '-';
                                },
                        ],
                        [
                                'attribute' => 'tempoentrada',
                                'label' => 'Tempo de Entrada',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                        ],
                        [
                                'attribute' => 'status',
                                'label' => 'Estado',
                                'value' => function ($model) {
                                    return match ($model->status) {
                                        'Em espera' => '⏳ A aguardar Atendimento',
                                        'Atendida'   => '✅ Atendida',
                                        'Encerrada'  => '❌ Encerrada',
                                        default => Html::encode($model->status),
                                    };
                                },
                        ],
                        [
                                'label' => 'Paciente',
                                'value' => $model->userprofile->nome ?? '—',
                        ],
                        [
                                'label' => 'Triagem Associada',
                                'format' => 'html',
                                'value' => $model->triagem
                                        ? Html::a('Ver Triagem #' . $model->triagem->id, ['triagem/view', 'id' => $model->triagem->id], ['class' => 'text-success fw-semibold'])
                                        : '—',
                        ],
                ],
        ]) ?>

        <div class="mt-4 text-center">
            <?= Html::a('<i class="bi bi-pencil-square me-1"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-warning text-white me-2']) ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i> Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                            'confirm' => 'Tens a certeza que queres eliminar esta pulseira?',
                            'method' => 'post',
                    ],
            ]) ?>
        </div>
    </div>
</div>
