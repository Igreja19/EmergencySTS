<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */
/** @var array $roleOptions */
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile.css');

?>

<?php $form = ActiveForm::begin(); ?>

<h5 class="fw-bold text-success mb-3">
    <i class="bi bi-person-lines-fill me-2"></i> Dados do Utilizador
</h5>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'nome')->textInput([
                'maxlength' => true,
                'placeholder' => 'Nome completo'
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'email')->input('email', [
                'placeholder' => 'Email'
        ]) ?>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'telefone')->textInput([
                'maxlength' => true,
                'placeholder' => '9XXXXXXXX'
        ]) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'nif')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'sns')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <?= $form->field($model, 'genero')->dropDownList([
                'M' => 'Masculino',
                'F' => 'Feminino',
                'O' => 'Outro',
        ], ['prompt' => '— Selecionar —']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'datanascimento')
                ->input('date', [
                        'onkeydown' => 'return false',
                        'onpaste' => 'return false',
                        'onclick' => 'this.showPicker()', // força a abrir o calendario
                ]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'morada')->textInput([
                'maxlength' => true,
                'placeholder' => 'Morada'
        ]) ?>
    </div>
</div>

<!-- 🔹 Campo de Role -->
<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'role')->dropDownList(
                $roleOptions ?? [], // garante que não dá erro se a variável não existir
                ['prompt' => '— Selecionar função —']
        )->label('Função / Role') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => $model->isNewRecord
                        ? 'Definir password'
                        : 'Deixe em branco para manter a password atual',
        ]) ?>
    </div>
</div>
<div class="mt-4 d-flex justify-content-between align-items-center">

    <!-- Esquerda: Guardar / Cancelar -->
    <div class="d-flex gap-2">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <!-- ️ Direita: Ativar / Desativar -->
    <div>
        <?php if ($model->isAtivo()): ?>
            <?= Html::a(
                    '<i class="bi bi-person-x"></i> Desativar',
                    ['desativar', 'id' => $model->id],
                    [
                            'class' => 'btn btn-danger fw-semibold',
                            'data-confirm' => 'Tens a certeza que queres desativar este utilizador?',
                            'data-method' => 'post',
                    ]
            ) ?>
        <?php else: ?>
            <?= Html::a(
                    '<i class="bi bi-person-check"></i> Ativar',
                    ['ativar', 'id' => $model->id],
                    [
                            'class' => 'btn btn-success fw-semibold',
                            'data-confirm' => 'Queres reativar este utilizador?',
                            'data-method' => 'post',
                    ]
            ) ?>
        <?php endif; ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
