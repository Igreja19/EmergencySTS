<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Userprofile;

/** @var yii\web\View $this */
/** @var common\models\UserProfileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Perfis de Utilizador';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .profile-index-card {
        border-radius: 18px;
        border: none;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        background-color: #fff;
    }
    .profile-index-card .card-header {
        background: linear-gradient(90deg, #198754, #20c997);
        color: #fff;
        font-weight: 600;
        font-size: 1.1rem;
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    /* Barra de pesquisa */
    .search-bar {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .search-bar input {
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 10px 14px;
        width: 320px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: 0.2s;
    }
    .search-bar input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25,135,84,0.2);
        outline: none;
    }
    .search-bar button {
        border-radius: 10px;
        background-color: #198754;
        color: white;
        padding: 10px 16px;
        border: none;
        transition: 0.2s;
        font-weight: 500;
    }
    .search-bar button:hover {
        background-color: #157347;
        transform: translateY(-2px);
    }

    /* Cabeçalho da tabela */
    .table thead {
        background: linear-gradient(90deg, #198754, #20c997);
        color: #fff !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }
    .table thead th a {
        color: black !important;
        text-decoration: none !important;
    }
    .table thead th a:hover {
        color: #d1e7dd !important;
    }
    .table tbody tr:hover {
        background-color: #f8fff9;
    }
    .table td {
        vertical-align: middle;
    }

    /* Badges */
    .badge-role {
        font-weight: 500;
        border-radius: 10px;
        padding: 6px 10px;
    }

    /* Botões de ação */
    .action-buttons a {
        margin: 0 3px;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border: 1px solid #dee2e6;
        background-color: #fff;
        transition: all 0.2s ease;
    }
    .action-buttons a i {
        pointer-events: none;
    }
    .action-buttons a:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .btn-view {
        color: #0d6efd;
    }
    .btn-view:hover {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .btn-edit {
        color: #198754;
    }
    .btn-edit:hover {
        background-color: #198754;
        color: #fff;
        border-color: #198754;
    }
    .btn-delete {
        color: #dc3545;
    }
    .btn-delete:hover {
        background-color: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }
");
?>

<div class="container-fluid py-4">

    <div class="card profile-index-card">
        <div class="card-header">
            <span><i class="bi bi-people-fill me-2"></i><?= Html::encode($this->title) ?></span>
            <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Novo Perfil', ['create'], [
                    'class' => 'btn btn-light text-success fw-semibold px-3 py-2 rounded-3 shadow-sm',
            ]) ?>
        </div>

        <div class="card-body">

            <!-- 🔍 Barra de pesquisa -->
            <div class="search-bar">
                <form method="get" action="<?= Url::to(['index']) ?>">
                    <input type="text" name="UserProfileSearch[q]" placeholder="Pesquisar..." value="<?= Html::encode(Yii::$app->request->get('UserProfileSearch')['q'] ?? '') ?>">
                    <button type="submit"><i class="bi bi-search me-1"></i> Procurar</button>
                </form>
            </div>

            <!-- 📋 Tabela -->
            <div class="table-responsive">
                <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'summary' => false,
                        'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                        'columns' => [
                                ['class' => 'yii\grid\SerialColumn', 'header' => '#'],

                                [
                                        'attribute' => 'nome',
                                        'format' => 'raw',
                                        'value' => fn($model) =>
                                                '<div class="fw-semibold">' . Html::encode($model->nome) . '</div>'
                                                . '<small class="text-muted"><i class="bi bi-envelope me-1"></i>'
                                                . Html::encode($model->email) . '</small>',
                                ],
                                [
                                        'attribute' => 'telefone',
                                        'format' => 'raw',
                                        'value' => fn($model) => $model->telefone ?: '<span class="text-muted">—</span>',
                                ],
                                [
                                        'attribute' => 'nif',
                                        'format' => 'raw',
                                        'value' => fn($model) => $model->nif ?: '<span class="text-muted">—</span>',
                                ],
                                [
                                        'label' => 'Género',
                                        'format' => 'raw',
                                        'value' => fn($model) => match ($model->genero) {
                                            'M' => '<span class="badge bg-primary badge-role"><i class="bi bi-gender-male me-1"></i>Masculino</span>',
                                            'F' => '<span class="badge bg-danger badge-role"><i class="bi bi-gender-female me-1"></i>Feminino</span>',
                                            'O' => '<span class="badge bg-secondary badge-role">Outro</span>',
                                            default => '<span class="badge bg-light text-muted badge-role">—</span>',
                                        },
                                ],
                                [
                                        'label' => 'Função',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            $roles = Yii::$app->authManager->getRolesByUser($model->user_id);
                                            $roleName = !empty($roles) ? array_keys($roles)[0] : null;
                                            return match ($roleName) {
                                                'admin' => '<span class="badge bg-danger badge-role"><i class="bi bi-shield-lock"></i> Admin</span>',
                                                'medico' => '<span class="badge bg-primary badge-role"><i class="bi bi-heart-pulse"></i> Médico</span>',
                                                'enfermeiro' => '<span class="badge bg-success badge-role"><i class="bi bi-bandaid"></i> Enfermeiro</span>',
                                                default => '<span class="badge bg-secondary badge-role"><i class="bi bi-person"></i> Utilizador</span>',
                                            };
                                        },
                                ],
                                [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Ações',
                                        'headerOptions' => ['style' => 'width:160px; text-align:center;'],
                                        'contentOptions' => ['style' => 'text-align:center; vertical-align:middle;'],
                                        'template' => '<div class="action-buttons">{view} {update} {delete}</div>',
                                        'buttons' => [
                                                'view' => fn($url) => Html::a('<i class="bi bi-eye"></i>', $url, [
                                                        'class' => 'btn-view',
                                                        'title' => 'Ver detalhes',
                                                ]),
                                                'update' => fn($url) => Html::a('<i class="bi bi-pencil"></i>', $url, [
                                                        'class' => 'btn-edit',
                                                        'title' => 'Editar perfil',
                                                ]),
                                                'delete' => fn($url) => Html::a('<i class="bi bi-trash"></i>', $url, [
                                                        'class' => 'btn-delete',
                                                        'title' => 'Eliminar perfil',
                                                        'data-confirm' => 'Tem a certeza que deseja eliminar este perfil?',
                                                        'data-method' => 'post',
                                                ]),
                                        ],
                                ],
                        ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
