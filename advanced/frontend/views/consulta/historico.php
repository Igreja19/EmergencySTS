<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Consulta[] $consultas */
/** @var int $total */
/** @var string $ultimaVisita */

$this->title = 'Histórico de Consultas';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/consulta/historico.css');
?>

<div class="container py-4">
    <h4 class="fw-semibold mb-1"><?= Html::encode($this->title) ?></h4>
    <p class="text-muted mb-4">Consultas realizadas</p>

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
    </div>

    <?php foreach ($consultas as $c): ?>
        <?php
        // 🔹 prioridade vem da pulseira
        $prioridade = $c->triagem->pulseira->prioridade ?? 'Pendente';

        $badgeClasses = [
                'Vermelho' => 'danger',
                'Laranja'  => 'laranja',
                'Amarelo'  => 'warning',
                'Verde'    => 'success',
                'Azul'     => 'primary',
                'Pendente' => 'secondary',
        ];

        $badgeClass = $badgeClasses[$prioridade] ?? 'secondary';
        ?>

        <div class="card shadow-sm border-0 rounded-4 mb-3 p-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                    <h6 class="fw-semibold mb-1">
                        <span class="badge bg-<?= $badgeClass ?>">
                            <?= Html::encode($prioridade) ?>
                        </span>

                        <span class="badge bg-secondary ms-1">
                            <?= Html::encode($c->estado) ?>
                        </span>
                    </h6>

                    <small class="text-secondary">
                        <?= Yii::$app->formatter->asDatetime(
                                $c->data_consulta,
                                'php:d/m/Y H:i'
                        ) ?>
                    </small>
                </div>

                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <?= Html::a(
                            '<i class="bi bi-eye"></i> Ver',
                            ['ver', 'id' => $c->id],
                            ['class' => 'btn btn-outline-dark btn-sm rounded-pill']
                    ) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
