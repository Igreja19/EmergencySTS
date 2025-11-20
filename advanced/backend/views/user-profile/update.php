<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */
/** @var array $roleOptions */

$this->title = 'Editar Perfil';
$this->params['breadcrumbs'][] = ['label' => 'Utilizadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Ver Perfil #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile/update.css');

?>

<div class="userprofile-update">
    <h1><i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?></h1>

    <div class="userprofile-form">
        <?= $this->render('_form', [
                'model' => $model,
                'roleOptions' => $roleOptions,
        ]) ?>
    </div>

    <div class="text-center mt-3">
        <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-back']) ?>
    </div>
</div>
