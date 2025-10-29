<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use Yii;

/** @var yii\web\View $this */
/** @var common\models\Userprofile $model */
/** @var yii\widgets\ActiveForm $form */

$roles = Yii::$app->authManager->getRoles();
$roleOptions = [];
foreach ($roles as $name => $role) {
    $roleOptions[$name] = ucfirst($name);
}
?>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <h5 class="fw-bold text-secondary mb-3">
            <i class="bi bi-person-fill text-success me-2"></i> Dados do Perfil
        </h5>

        <div class="row g-3">
            <div class="col-md-6">
                <?= $form->field($model, 'nome')->textInput(['maxlength' => true, 'placeholder' => 'Nome completo'])->label('Nome') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nif')->textInput([
                        'maxlength' => 9,
                        'pattern' => '\d*',
                        'inputmode' => 'numeric',
                        'placeholder' => 'NIF'
                ])->label('NIF') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'datanascimento')->input('date')->label('Data de Nascimento') ?>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <?= $form->field($model, 'genero')->dropDownList([
                        'M' => 'Masculino',
                        'F' => 'Feminino',
                        'O' => 'Outro'
                ], ['prompt' => 'Selecione...', 'class' => 'form-select'])->label('Género') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'sns')->textInput(['maxlength' => true, 'placeholder' => 'Número SNS'])->label('SNS') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'telefone')->textInput([
                        'maxlength' => true,
                        'pattern' => '\d*',
                        'inputmode' => 'tel',
                        'placeholder' => 'Telefone'
                ])->label('Telefone') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'email')->input('email')->label('Email') ?>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-12">
                <?= $form->field($model, 'morada')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Morada completa'
                ])->label('Morada') ?>
            </div>
        </div>

        <?php if (Yii::$app->user->can('admin')): ?>
            <div class="mt-4">
                <h6 class="fw-bold text-secondary mb-2">
                    <i class="bi bi-shield-lock-fill text-success me-1"></i> Função / Role
                </h6>

                <div class="p-3 border rounded-3 bg-light shadow-sm">
                    <?= $form->field($model, 'role')->dropDownList(
                            $roleOptions,
                            [
                                    'prompt' => 'Selecione uma função...',
                                    'class' => 'form-select form-select-lg border-success shadow-sm',
                                    'style' => 'font-weight:500; color:#2e7d32; background-color:#f8fff9;'
                            ]
                    )->label(false) ?>
                    <small class="text-muted">
                        Escolha o tipo de acesso que este utilizador terá no sistema.
                    </small>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group mt-4 text-end">
            <?= Html::submitButton(
                    $model->isNewRecord ? '<i class="bi bi-check-circle me-1"></i> Criar' : '<i class="bi bi-save2-fill me-1"></i> Guardar',
                    ['class' => 'btn btn-success px-4']
            ) ?>
            <?= Html::a('<i class="bi bi-x-circle me-1"></i> Cancelar', ['index'], ['class'=>'btn btn-outline-secondary px-3']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
