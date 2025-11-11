<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */
/** @var array $roleOptions */

$this->title = 'Criar Utilizador';
$this->params['breadcrumbs'][] = ['label' => 'Utilizadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile.css');
?>
<div class="userprofile-create">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mb-0 text-success"><i class="bi bi-person-plus me-2"></i><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <div class="userprofile-form">
        <?= $this->render('_form', ['model' => $model, 'roleOptions' => $roleOptions,
        ]) ?>
    </div>
</div>
