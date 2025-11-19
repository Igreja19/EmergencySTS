<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\UserProfile $model */

$this->title = $model->isNewRecord ? 'Criar Perfil' : 'Editar Perfil';
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/user-profile/_form.css');
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="profile-page d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="form card shadow-sm border-0 rounded-4 p-4 w-100">
        <div class="text-center mb-4">
            <span class="badge bg-light text-success px-3 py-2 fw-semibold">EmergencySTS</span>
            <h3 class="fw-bold text-success mt-3">
                <i class="bi bi-person-badge me-2"></i><?= Html::encode($this->title) ?>
            </h3>
            <p class="text-muted">Atualize as suas informações pessoais abaixo.</p>
        </div>

        <?php $form = ActiveForm::begin([
                'id' => 'userprofile-form',
                'options' => ['class' => 'needs-validation'],
        ]); ?>

        <!-- 🔥 MOSTRAR ERROS DE VALIDAÇÃO -->
        <?= $form->errorSummary($model, [
                'class' => 'alert alert-danger mb-4',
        ]); ?>

        <!-- IDs escondidos -->
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>

        <!-- DADOS PESSOAIS -->
        <h6 class="fw-bold text-success mt-2 mb-3">Dados Pessoais</h6>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'nome')
                        ->textInput(['maxlength' => true, 'placeholder' => 'Nome completo'])
                        ->label('<i class="bi bi-person me-2"></i> Nome Completo') ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'datanascimento')
                        ->input('date', [
                                'min' => '1900-01-01',
                                'max' => date('Y-m-d'),
                        ])
                        ->label('<i class="bi bi-calendar me-2"></i> <span class="short-label">Data Nascimento</span>') ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'genero')->dropDownList([
                        'M' => 'Masculino',
                        'F' => 'Feminino',
                        'O' => 'Outro',
                ], ['prompt' => '— Selecionar —']) ?>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'email')
                        ->input('email', ['maxlength' => true, 'placeholder' => 'o.seu@email.com'])
                        ->label('<i class="bi bi-envelope me-2"></i> Email') ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'telefone')
                        ->textInput(['maxlength' => true, 'placeholder' => 'Telefone'])
                        ->label('<i class="bi bi-telephone me-2"></i> Telefone') ?>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <?= $form->field($model, 'morada')
                        ->textInput(['maxlength' => true, 'placeholder' => 'Rua, nº, andar, cidade...'])
                        ->label('<i class="bi bi-house-door me-2"></i> Morada') ?>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <?= $form->field($model, 'nif')
                        ->textInput(['maxlength' => true, 'placeholder' => 'NIF'])
                        ->label('<i class="bi bi-credit-card-2-front me-2"></i> NIF') ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'sns')
                        ->textInput(['maxlength' => true, 'placeholder' => 'Número de Utente (SNS)'])
                        ->label('<i class="bi bi-hospital me-2"></i> Nº SNS') ?>
            </div>
        </div>

        <!-- BOTÕES -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="<?= Yii::$app->urlManager->createUrl(['user-profile/view', 'id' => $model->id ?: 0]) ?>"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left-short me-1"></i> Voltar
            </a>

            <?= Html::submitButton(
                    ($model->isNewRecord
                            ? '<i class="bi bi-save me-2"></i> Criar Perfil'
                            : '<i class="bi bi-check2-circle me-2"></i> Guardar Alterações'),
                    ['class' => 'btn btn-success px-4 fw-semibold shadow-sm']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>