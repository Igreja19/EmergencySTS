<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Formulário Clínico - EmergencySTS';
$userProfile = Yii::$app->user->identity->userprofile;
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h3 class="fw-bold text-success mt-3">Formulário Clínico</h3>
        <p class="text-muted">Os seus <dados></dados> foram preenchidos automaticamente com base no seu perfil.</p>
    </div>

    <div class="mx-auto card shadow-sm border-0 rounded-4 p-4" style="max-width: 850px;">
        <?php $form = ActiveForm::begin([
                'id' => 'form-triagem',
                'action' => ['triagem/formulario'],
                'method' => 'post'
        ]); ?>

        <!-- 🔹 DADOS PESSOAIS -->
        <h6 class="fw-bold text-success mt-2 mb-3">Dados Pessoais</h6>
        <div class="row g-3 mb-3">

            <!-- Nome -->
            <div class="col-md-6">
                <label class="form-label fw-semibold text-success">
                    <i class="bi bi-person me-2"></i> Nome Completo
                </label>
                <input type="text" class="form-control"
                       value="<?= Html::encode($userProfile->nome ?? '') ?>"
                       readonly>
            </div>

            <!-- Data de Nascimento -->
            <div class="col-md-3">
                <label class="form-label fw-semibold text-success">
                    <i class="bi bi-calendar me-2"></i> Data de Nascimento
                </label>
                <input type="date" class="form-control"
                       value="<?= Html::encode($userProfile->datanascimento ?? '') ?>"
                       min="1900-01-01"
                       max="<?= date('Y-m-d') ?>"
                       readonly>
            </div>

            <!-- SNS -->
            <div class="col-md-3">
                <label class="form-label fw-semibold text-success">
                    <i class="bi bi-hospital me-2"></i> Número de Utente (SNS)
                </label>
                <input type="text" class="form-control"
                       value="<?= Html::encode($userProfile->sns ?? '') ?>"
                       readonly>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <!-- Telefone -->
            <div class="col-md-6">
                <label class="form-label fw-semibold text-success">
                    <i class="bi bi-telephone me-2"></i> Telefone
                </label>
                <input type="text" class="form-control"
                       value="<?= Html::encode($userProfile->telefone ?? '') ?>"
                       readonly>
            </div>

            <!-- Motivo da Consulta -->
            <div class="col-md-6">
                <?= $form->field($model, 'motivoconsulta')
                        ->textInput(['placeholder' => 'Motivo da consulta'])
                        ->label('<i class="bi bi-chat-dots me-2"></i> Motivo da Consulta') ?>
            </div>
        </div>

        <!-- 🔹 SINTOMAS E QUEIXAS -->
        <h6 class="fw-bold text-success section-spacing">Sintomas e Queixas</h6>
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
                        ->dropDownList([
                                0 => '0 - Sem Dor',
                                1 => '1 - Muito Leve',
                                2 => '2 - Leve',
                                3 => '3 - Moderada',
                                4 => '4 - Moderada a Forte',
                                5 => '5 - Forte',
                                6 => '6 - Bastante Forte',
                                7 => '7 - Muito Forte',
                                8 => '8 - Intensa',
                                9 => '9 - Muito Intensa',
                                10 => '10 - Insuportável'
                        ], [
                                'prompt' => 'Selecione a intensidade da dor',
                                'class' => 'form-select rounded-3 shadow-sm'
                        ])
                        ->label('<i class="bi bi-emoji-expressionless me-2"></i> Intensidade da Dor (0-10)') ?>
            </div>
        </div>

        <!-- 🔹 CONDIÇÕES, ALERGIAS E MEDICAÇÃO -->
        <h6 class="fw-bold text-success section-spacing">Informações Adicionais</h6>
        <?= $form->field($model, 'alergias')
                ->textarea(['rows' => 2, 'placeholder' => 'Alergias conhecidas...'])
                ->label('<i class="bi bi-exclamation-triangle me-2"></i> Alergias Conhecidas') ?>

        <?= $form->field($model, 'medicacao')
                ->textarea(['rows' => 2, 'placeholder' => 'Medicação atual...'])
                ->label('<i class="bi bi-capsule me-2"></i> Medicação Atual') ?>

        <!-- 🔹 BOTÃO -->
        <?= Html::hiddenInput('Triagem[userprofile_id]', $userProfile->id) ?>
        <div class="text-center mt-4">
            <?= Html::submitButton('<i class="bi bi-save me-2"></i> Submeter Formulário', [
                    'class' => 'btn btn-success btn-lg px-5 py-3 fw-semibold shadow-sm submit-btn'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <script>
        document.querySelector('#form-triagem').addEventListener('submit', function() {
            const btn = document.querySelector('.submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> A enviar...';
        });
    </script>
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

    /* Inputs e selects uniformes */
    .form-control, .form-select, select {
        border-radius: 10px !important;
        padding: 10px 14px;
        height: 44px;
    }

    textarea.form-control {
        resize: none;
        height: auto;
    }

    /* ✅ Labels com espaçamento e cor */
    .form-label, label {
        font-weight: 600;
        color: #198754; /* verde original */
        margin-top: 6px; /* pequeno espaço entre a caixa anterior e o label */
        margin-bottom: 6px; /* espaço entre o label e o input abaixo */
        display: block;
    }

    /* Alinhamento visual consistente */
    .row.g-3 > [class*="col-"] {
        margin-bottom: 10px;
    }

    /* Secções principais */
    h6 {
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #198754;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    /* Botões */
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
    /* 🔹 Corrige desalinhamento de colunas com labels longas */
    .row.g-3 .col-md-3,
    .row.g-3 .col-md-6 {
        display: flex;
        flex-direction: column;
        justify-content: flex-end; /* alinha todas as caixas pela base */
    }

    .form-select {
        border-radius: 10px !important;
        padding: 10px 14px;
        height: 44px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all .25s ease;
    }
    .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.15rem rgba(25,135,84,.25);
    }
</style>


