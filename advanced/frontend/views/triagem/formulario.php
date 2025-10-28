<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Formulário Clínico - EmergencySTS';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge bg-light text-success px-3 py-2 fw-semibold">Triagem Hospitalar</span>
        <h3 class="fw-bold text-success mt-3">Formulário Clínico</h3>
        <p class="text-muted">Preencha os dados do paciente para proceder à avaliação de prioridade.</p>
    </div>

    <div class="mx-auto card shadow-sm border-0 rounded-4 p-4" style="max-width: 850px;">
        <!-- FORMULÁRIO -->
        <?php $form = ActiveForm::begin([
                'id' => 'form-triagem',
                'action' => ['triagem/formulario'], // rota do controller
                'method' => 'post'
        ]); ?>

        <!-- DADOS PESSOAIS -->
        <h6 class="fw-bold text-success mt-2 mb-3">Dados Pessoais</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'nomecompleto')
                        ->textInput(['placeholder' => 'Nome completo'])
                        ->label('<i class="bi bi-person me-2"></i> Nome Completo') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'datanascimento')
                        ->input('date')
                        ->label('<i class="bi bi-calendar me-2"></i> Data de Nascimento') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'sns')
                        ->textInput(['placeholder' => 'Número SNS'])
                        ->label('<i class="bi bi-hospital me-2"></i> Número de Utente (SNS)') ?>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'telefone')
                        ->textInput(['placeholder' => 'Telefone'])
                        ->label('<i class="bi bi-telephone me-2"></i> Telefone') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'motivoconsulta')
                        ->textInput(['placeholder' => 'Motivo da consulta'])
                        ->label('<i class="bi bi-chat-dots me-2"></i> Motivo da Consulta') ?>
            </div>
        </div>

        <!-- 🔹 SINTOMAS E QUEIXAS -->
        <h6 class="fw-bold text-success mt-4 mb-3">Sintomas e Queixas</h6>
        <?= $form->field($model, 'queixaprincipal')
                ->textarea(['rows' => 3, 'placeholder' => 'Descreva a queixa principal...'])
                ->label('<i class="bi bi-clipboard2-pulse me-2"></i> Queixa Principal') ?>

        <?= $form->field($model, 'descricaosintomas')
                ->textarea(['rows' => 3, 'placeholder' => 'Descreva os sintomas apresentados...'])
                ->label('<i class="bi bi-body-text me-2"></i> Descrição dos Sintomas') ?>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'iniciosintomas')
                        ->input('datetime-local')
                        ->label('<i class="bi bi-clock-history me-2"></i> Início dos Sintomas') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'intensidadedor')
                        ->input('number', ['min' => 0, 'max' => 10, 'placeholder' => '0 a 10'])
                        ->label('<i class="bi bi-emoji-expressionless me-2"></i> Intensidade da Dor (0-10)') ?>
            </div>
        </div>

        <!-- 🔹 CONDIÇÕES, ALERGIAS E MEDICAÇÃO -->
        <h6 class="fw-bold text-success mt-4 mb-3">Informações Adicionais</h6>
        <?= $form->field($model, 'condicoes')
                ->textarea(['rows' => 2, 'placeholder' => 'Condições médicas conhecidas...'])
                ->label('<i class="bi bi-heart-pulse me-2"></i> Condições Médicas Conhecidas') ?>

        <?= $form->field($model, 'alergias')
                ->textarea(['rows' => 2, 'placeholder' => 'Alergias conhecidas...'])
                ->label('<i class="bi bi-exclamation-triangle me-2"></i> Alergias Conhecidas') ?>

        <?= $form->field($model, 'medicacao')
                ->textarea(['rows' => 2, 'placeholder' => 'Medicação atual...'])
                ->label('<i class="bi bi-capsule me-2"></i> Medicação Atual') ?>

        <!-- 🔹 TRIAGEM -->
        <h6 class="fw-bold text-success mt-4 mb-3">Prioridade e Triagem</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'prioridadeatribuida')
                        ->dropDownList([
                                'Vermelha' => '🔴 Vermelha - Emergente',
                                'Laranja' => '🟠 Laranja - Muito Urgente',
                                'Amarela' => '🟡 Amarela - Urgente',
                                'Verde' => '🟢 Verde - Pouco Urgente',
                                'Azul' => '🔵 Azul - Não Urgente',
                        ], ['prompt' => 'Selecione a prioridade'])
                        ->label('<i class="bi bi-flag me-2"></i> Prioridade Atribuída') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'datatriagem')
                        ->input('datetime-local')
                        ->label('<i class="bi bi-calendar-event me-2"></i> Data da Triagem') ?>
            </div>
        </div>

        <?= $form->field($model, 'discriminacaoprincipal')
                ->textInput(['placeholder' => 'Discriminação / Motivo principal'])
                ->label('<i class="bi bi-journal-text me-2"></i> Discriminação Principal') ?>

        <!-- 🔹 BOTÃO -->
        <div class="text-center mt-4">
            <?= Html::submitButton('<i class="bi bi-save me-2"></i> Submeter Formulário', [
                    'class' => 'btn btn-success btn-lg px-5 py-3 fw-semibold shadow-sm submit-btn'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<!-- 🔹 CSS -->
<style>
    body {
        background: linear-gradient(180deg, #f8fff9 0%, #eef8ef 100%);
    }

    .card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 18px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.1);
    }

    .form-control, select {
        border-radius: 10px !important;
        padding: 10px 14px;
    }

    textarea.form-control {
        resize: none;
    }

    label {
        font-weight: 600;
        color: #198754;
    }

    h6 {
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .btn-success {
        background-color: #198754 !important;
        border: none;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background-color: #16a34a !important;
        box-shadow: 0 4px 15px rgba(22, 163, 74, 0.4);
        transform: translateY(-2px);
    }
</style>

<!-- Bootstrap Icons -->
