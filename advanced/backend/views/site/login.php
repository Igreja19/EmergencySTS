<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Acesso Restrito';
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/site.css');
?>

<div class="login-container">
    <div class="login-card card border-0 shadow-lg rounded-4">
        <div class="card-body text-center">

            <!-- 🔹 Logótipo / Ícone -->
            <div class="mb-4">
                <div class="d-flex justify-content-center mb-3">
                    <div class="login-icon shadow-sm">
                        <img src="<?= Yii::getAlias('@web') ?>/img/login.png"
                             alt="Segurança"
                             class="img-fluid p-2"
                             style="max-width: 90px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));">
                    </div>
                </div>
                <h3 class="fw-bold text-success mt-3 mb-1">Acesso Restrito</h3>
                <p class="text-muted small">Área exclusiva para funcionários do hospital</p>
            </div>

            <!-- 🔹 Formulário -->
            <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'layout' => 'default',
                    'options' => ['class' => 'text-start login-form']
            ]); ?>

            <?= $form->field($model, 'username', [
                    'inputOptions' => [
                            'class' => 'form-control rounded-3 shadow-sm',
                            'placeholder' => 'Introduza o seu username'
                    ],
            ])->label('<i class="bi bi-person-fill me-1"></i> Nome de Utilizador', [
                    'class' => 'form-label fw-semibold text-success'
            ]) ?>

            <?= $form->field($model, 'password', [
                    'inputOptions' => [
                            'class' => 'form-control rounded-3 shadow-sm',
                            'placeholder' => '********'
                    ],
            ])->passwordInput()
                    ->label('<i class="bi bi-lock-fill me-1"></i> Palavra-passe', [
                            'class' => 'form-label fw-semibold text-success'
                    ]) ?>

            <div class="d-grid mt-4">
                <?= Html::submitButton('<i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sessão', [
                        'class' => 'btn btn-success btn-lg rounded-3 fw-semibold shadow-sm position-relative overflow-hidden'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <!-- 🔹 Rodapé / Alerta -->
            <div class="alert alert-light border mt-4 small shadow-sm text-start" role="alert">
                <i class="bi bi-shield-lock-fill text-success me-2"></i>
                <strong>Acesso Seguro:</strong> Apenas funcionários autorizados podem aceder.
            </div>

            <div class="text-center mt-3">
                <?= Html::a('<i class="bi bi-question-circle me-1"></i> Esqueceu-se da palavra-passe?',
                        ['site/request-password-reset'],
                        ['class' => 'text-success text-decoration-none fw-semibold']) ?>
            </div>

        </div>
    </div>
</div>
