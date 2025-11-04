<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */

$this->title = 'Criar Perfil de Utilizador';
$this->params['breadcrumbs'][] = ['label' => 'Perfis de Utilizador', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="userprofile-create">

    <?php // Removido o título visível no frontend ?>

    <?= $this->render('_form', [
            'model' => $model,
    ]) ?>

</div>
