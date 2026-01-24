<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'O Meu Perfil';
\yii\web\YiiAsset::register($this);
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile/meu-perfil.css');

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge bg-light text-success px-3 py-2 fw-semibold shadow-sm">EmergencySTS</span>
        <h3 class="fw-bold text-success mt-3">
            <i class="bi bi-person-circle me-2"></i><?= Html::encode($this->title) ?>
        </h3>
        <p class="text-muted">Visualize e atualize as informações associadas à sua conta.</p>
    </div>

    <div class="mx-auto card shadow-lg border-0 rounded-4 p-4" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h5 class="fw-bold text-success mb-0">
                <i class="bi bi-person-lines-fill me-2"></i>Dados Pessoais
            </h5>
            <div class="text-end">
                <?= Html::a('<i class="bi bi-pencil-square me-1"></i> Editar', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-success btn-sm fw-semibold shadow-sm me-1'
                ]) ?>
            </div>
        </div>

        <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-borderless align-middle table-modern-profile'],
                'attributes' => [
                        [
                                'attribute' => 'nome',
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
                        ],
                        [
                                'attribute' => 'morada',
                                'label' => '<i class="bi bi-geo-alt-fill me-2"></i> Morada',
                                'value' => $model->morada ?: '—',
                        ],
                        [
                                'attribute' => 'genero',
                                'label' => '<i class="bi bi-gender-ambiguous me-2"></i> Género',
                                'value' => match ($model->genero) {
                                    'M' => 'Masculino',
                                    'F' => 'Feminino',
                                    'O' => 'Outro',
                                    default => '—',
                                },
                        ],
                        [
                                'attribute' => 'datanascimento',
                                'label' => '<i class="bi bi-calendar3 me-2"></i> Data de Nascimento',
                                'value' => Yii::$app->formatter->asDate($model->datanascimento, 'php:d/m/Y'),
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
                                'label' => '<i class="bi bi-shield-lock-fill me-2"></i> Função / Role',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $roles = Yii::$app->authManager->getRolesByUser($model->user_id);
                                    if (!empty($roles)) {
                                        return '<span class="badge bg-success bg-opacity-75 px-3 py-2">'
                                                . ucfirst(array_keys($roles)[0]) . '</span>';
                                    }
                                    return '<span class="text-muted">—</span>';
                                },
                        ],
                ],
        ]) ?>
    </div>
</div>
