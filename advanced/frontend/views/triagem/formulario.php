<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Formulário Clínico - EmergencySTS';
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/triagem/formulario.css');

if (Yii::$app->user->isGuest) {
    echo "<div class='container py-5 text-center'>
            <div class='alert alert-danger'>
                ⚠ Precisa de iniciar sessão para preencher o formulário clínico.
            </div>
          </div>";
    return;
}

$user = Yii::$app->user->identity;
$userProfile = $user->userprofile ?? null;

if (!$userProfile) {
    echo "<div class='container py-5 text-center'>
            <div class='alert alert-danger'>
                ⚠ O seu perfil está incompleto.<br>
                Por favor preencha o seu perfil ou contacte um administrador.
            </div>
          </div>";
    return;
}
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h3 class="fw-bold text-success mt-3">Formulário Clínico</h3>
        <p class="text-muted">Os seus dados foram preenchidos automaticamente com base no seu perfil.</p>
    </div>

    <div class="form mx-auto card shadow-sm border-0 rounded-4 p-4">

        <?php $form = ActiveForm::begin([
                'id' => 'form-triagem',
                'action' => ['triagem/formulario'],
                'method' => 'post'
        ]); ?>

        <h6 class="fw-bold text-success mt-2 mb-3">Dados Pessoais</h6>

        <div class="row g-3 mb-3">

            <div class="col-md-6">
                <label class="form-label fw-semibold text-success">
                    <i class="bi bi-person me-2"></i> Nome Completo
                </label>
                <input type="text" class="form-control"
                       value="<?= Html::encode($userProfile->nome ?? '') ?>"
                       readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold text-success">
                    <i class="bi bi-calendar me-2"></i> Data de Nascimento
                </label>
                <input type="date" class="form-control"
                       value="<?= Html::encode($userProfile->datanascimento ?? '') ?>"
                       min="1900-01-01"
                       max="<?= date('Y-m-d') ?>"
                       onkeydown="return false"
                       onpaste="return false"
                       onclick="this.showPicker()"
                >
            </div>

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
            <div class="col-md-6">
                <label class="form-label fw-semibold text-success">
                    <i class="bi bi-telephone me-2"></i> Telefone
                </label>
                <input type="text" class="form-control"
                       value="<?= Html::encode($userProfile->telefone ?? '') ?>"
                       readonly>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'motivoconsulta')
                        ->textInput(['placeholder' => 'Motivo da consulta'])
                        ->label('<i class="bi bi-chat-dots me-2"></i> Motivo da Consulta') ?>
            </div>
        </div>

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
                        ->input('datetime-local', [
                                'id' => 'triagem-iniciosintomas',
                                'onkeydown' => 'return false',
                                'onpaste' => 'return false',
                                'onclick' => 'this.showPicker()', // força a abrir o calendario
                        ])
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

        <h6 class="fw-bold text-success section-spacing">Informações Adicionais</h6>

        <?= $form->field($model, 'alergias')
                ->textarea(['rows' => 2, 'placeholder' => 'Alergias conhecidas...'])
                ->label('<i class="bi bi-exclamation-triangle me-2"></i> Alergias Conhecidas') ?>

        <?= $form->field($model, 'medicacao')
                ->textarea(['rows' => 2, 'placeholder' => 'Medicação atual...'])
                ->label('<i class="bi bi-capsule me-2"></i> Medicação Atual') ?>

        <?= Html::hiddenInput('Triagem[userprofile_id]', $userProfile->id) ?>
        <div class="text-center mt-4">
            <?= Html::submitButton('<i class="bi bi-save me-2"></i> Submeter Formulário', [
                    'class' => 'btn btn-success btn-lg px-5 py-3 fw-semibold shadow-sm submit-btn'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const campoData = document.querySelector('#triagem-iniciosintomas');

            if (campoData) {
                const anoAtual = new Date().getFullYear();
                const anoMinimo = anoAtual - 100;

                campoData.addEventListener('input', function () {
                    const valor = campoData.value;

                    if (valor.length >= 4) {
                        const ano = parseInt(valor.substring(0, 4));

                        if (isNaN(ano) || ano < anoMinimo || ano > anoAtual) {
                            campoData.setCustomValidity(
                                `O ano deve estar entre ${anoMinimo} e ${anoAtual}.`
                            );
                        } else {
                            campoData.setCustomValidity("");
                        }
                    }
                });
            }
        });

        document.querySelector('#form-triagem').addEventListener('submit', function() {
            const btn = document.querySelector('.submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> A enviar...';
        });
    </script>
</div>

<?php
$this->registerJsFile(
        Yii::$app->request->baseUrl . '/js/triagem/formulario.js',
        ['depends' => [\yii\web\JqueryAsset::class]]
);
?>
