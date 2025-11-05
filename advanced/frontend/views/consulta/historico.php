<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Consulta[] $consultas */
/** @var int $total */
/** @var string $ultimaVisita */
/** @var string $prioridadeMaisComum */

$this->title = 'Histórico de Consultas';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-4">
    <h4 class="fw-semibold mb-1"><?= Html::encode($this->title) ?></h4>
    <p class="text-muted mb-4">Consultas realizadas e em curso</p>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center py-3">
                <h6 class="text-muted mb-1">Total</h6>
                <h4 class="fw-bold"><?= $total ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center py-3">
                <h6 class="text-muted mb-1">Última Visita</h6>
                <h4 class="fw-bold"><?= Html::encode($ultimaVisita) ?></h4>
            </div>
        </div>
        <!--<div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center py-3">
                <h6 class="text-muted mb-1">Prioridade Mais Comum</h6>
                <h4 class="fw-bold"><?php // Html::encode($prioridadeMaisComum) ?></h4>
            </div>
        </div>-->
    </div>

    <?php foreach ($consultas as $c): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-3 p-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                    <h6 class="fw-semibold mb-1">
                        Triagem #<?= Html::encode($c->triagem_id) ?>
                        <span class="badge bg-<?= strtolower($c->prioridade) ?> ms-1"><?= Html::encode($c->prioridade) ?></span>
                        <span class="badge bg-secondary ms-1"><?= Html::encode($c->estado) ?></span>
                    </h6>
                    <p class="text-muted mb-1"><?= Html::encode($c->motivo ?: 'Sem motivo registado') ?></p>
                    <small class="text-secondary"><?= Yii::$app->formatter->asDatetime($c->data_consulta, 'php:d/m/Y H:i') ?></small>
                </div>
                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <?= Html::a('<i class="bi bi-eye"></i> Ver', ['ver', 'id' => $c->id], ['class' => 'btn btn-outline-dark btn-sm rounded-pill']) ?>
                    <?php if ($c->estado !== 'Encerrada'): ?>
                        <?= Html::a('<i class="bi bi-check2-circle"></i> Encerrar', ['encerrar', 'id' => $c->id], [
                                'class' => 'btn btn-outline-success btn-sm rounded-pill',
                                'data-confirm' => 'Tem a certeza que pretende encerrar esta consulta?',
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
