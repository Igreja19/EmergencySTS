<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Perfis de Utilizador', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$roleOptions = Yii::$app->authManager->getRolesByUser($model->user_id);
$roleName = !empty($roleOptions) ? array_keys($roleOptions)[0] : null;

$roleBadge = match ($roleName) {
    'admin' => '<span class="badge bg-danger"><i class="bi bi-shield-lock"></i> Admin</span>',
    'medico' => '<span class="badge bg-primary"><i class="bi bi-heart-pulse"></i> Médico</span>',
    'enfermeiro' => '<span class="badge bg-success"><i class="bi bi-bandaid"></i> Enfermeiro</span>',
    default => '<span class="badge bg-secondary">Utilizador</span>',
};

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile/view.css');
?>

<div class="container py-4">
    <div class="profile-view">

        <div class="profile-header">
            <div>
                <div class="profile-title"><?= Html::encode($model->nome) ?></div>
                <div class="profile-subtitle"><i class="bi bi-envelope"></i> <?= Html::encode($model->email) ?></div>
            </div>
            <div>
                <?= $roleBadge ?>
            </div>
        </div>

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

        <div class="profile-section">
            <h6><i class="bi bi-geo-alt-fill me-1"></i> Endereço</h6>
            <table class="table table-borderless">
                <tr>
                    <th>Morada</th>
                    <td><?= $model->morada ?: '<span class="text-muted">—</span>' ?></td>
                </tr>
            </table>
        </div>

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
