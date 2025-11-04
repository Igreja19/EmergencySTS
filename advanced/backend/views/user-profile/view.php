<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Perfis de Utilizador', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$roles = Yii::$app->authManager->getRolesByUser($model->user_id);
$roleName = !empty($roles) ? array_keys($roles)[0] : null;

$roleBadge = match ($roleName) {
    'admin' => '<span class="badge bg-danger"><i class="bi bi-shield-lock"></i> Admin</span>',
    'medico' => '<span class="badge bg-primary"><i class="bi bi-heart-pulse"></i> Médico</span>',
    'enfermeiro' => '<span class="badge bg-success"><i class="bi bi-bandaid"></i> Enfermeiro</span>',
    default => '<span class="badge bg-secondary">Utilizador</span>',
};

// CSS refinado
$this->registerCss("
    .profile-view {
        background: #ffffff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    }
    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e8f5ec;
        margin-bottom: 25px;
        padding-bottom: 10px;
    }
    .profile-title {
        font-weight: 700;
        color: #198754;
        font-size: 1.4rem;
    }
    .profile-subtitle {
        color: #6c757d;
        font-size: 0.95rem;
    }
    .profile-section {
        margin-bottom: 20px;
    }
    .profile-section h6 {
        color: #198754;
        font-weight: 600;
        border-left: 4px solid #198754;
        padding-left: 10px;
        margin-bottom: 12px;
    }
    .table td, .table th {
        vertical-align: middle !important;
        padding: 10px 8px;
    }
    .table th {
        color: #6c757d;
        width: 30%;
        font-weight: 500;
    }
    .table td {
        color: #212529;
        font-weight: 500;
    }
");
?>

<div class="container py-4">
    <div class="profile-view">

        <!-- Cabeçalho -->
        <div class="profile-header">
            <div>
                <div class="profile-title"><?= Html::encode($model->nome) ?></div>
                <div class="profile-subtitle"><i class="bi bi-envelope"></i> <?= Html::encode($model->email) ?></div>
            </div>
            <div>
                <?= $roleBadge ?>
            </div>
        </div>

        <!-- Secção: Dados Pessoais -->
        <div class="profile-section">
            <h6><i class="bi bi-person-lines-fill me-1"></i> Dados Pessoais</h6>
            <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-borderless align-middle mb-0'],
                    'template' => '<tr><th>{label}</th><td>{value}</td></tr>',
                    'attributes' => [
                            [
                                    'label' => 'Género',
                                    'value' => match ($model->genero) {
                                        'M' => 'Masculino',
                                        'F' => 'Feminino',
                                        'O' => 'Outro',
                                        default => '<span class="text-muted">—</span>',
                                    },
                                    'format' => 'raw',
                            ],
                            [
                                    'label' => 'Data de Nascimento',
                                    'value' => $model->datanascimento
                                            ? Yii::$app->formatter->asDate($model->datanascimento, 'php:d/m/Y')
                                            : '<span class="text-muted">—</span>',
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'telefone',
                                    'value' => $model->telefone ?: '<span class="text-muted">—</span>',
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'sns',
                                    'value' => $model->sns ?: '<span class="text-muted">—</span>',
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'nif',
                                    'value' => $model->nif ?: '<span class="text-muted">—</span>',
                                    'format' => 'raw',
                            ],
                    ],
            ]) ?>
        </div>

        <!-- Secção: Endereço -->
        <div class="profile-section">
            <h6><i class="bi bi-geo-alt-fill me-1"></i> Endereço</h6>
            <table class="table table-borderless">
                <tr>
                    <th>Morada</th>
                    <td><?= $model->morada ?: '<span class="text-muted">—</span>' ?></td>
                </tr>
            </table>
        </div>

        <!-- Secção: Estado -->
        <div class="profile-section">
            <h6><i class="bi bi-person-check-fill me-1"></i> Estado da Conta</h6>
            <table class="table table-borderless">
                <tr>
                    <th>Estado</th>
                    <td>
                        <?= match ($model->user->status ?? null) {
                            10 => '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Ativo</span>',
                            9 => '<span class="badge bg-warning text-dark"><i class="bi bi-pause-circle"></i> Inativo</span>',
                            0 => '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Eliminado</span>',
                            default => '<span class="badge bg-secondary">Desconhecido</span>',
                        } ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Botões -->
        <div class="text-end mt-4">
            <?= Html::a('<i class="bi bi-pencil"></i> Editar', ['update', 'id' => $model->id], [
                    'class' => 'btn btn-success px-4 py-2 shadow-sm me-2 rounded-3',
            ]) ?>
            <?= Html::a('<i class="bi bi-arrow-left"></i> Voltar', ['index'], [
                    'class' => 'btn btn-outline-secondary px-4 py-2 shadow-sm rounded-3',
            ]) ?>
        </div>
    </div>
</div>
