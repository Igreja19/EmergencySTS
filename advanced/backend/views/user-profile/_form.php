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

<div class="userprofile-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- Dados pessoais existentes -->
    <h5 class="fw-bold text-success mb-3">
        <i class="bi bi-person-lines-fill me-2"></i> Dados do Utilizador
    </h5>

    <div class="row g-3">
        <div class="col-md-6">
            <?= $form->field($model, 'nome')->textInput(['placeholder' => 'Nome completo']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->input('email', ['placeholder' => 'Email']) ?>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <?= $form->field($model, 'nif')->textInput(['placeholder' => 'NIF', 'maxlength' => 9]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'sns')->textInput(['placeholder' => 'Número SNS']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'telefone')->textInput(['placeholder' => 'Telefone']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'datanascimento')->input('date') ?>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-3">
            <?= $form->field($model, 'genero')->dropDownList([
                    'M' => 'Masculino',
                    'F' => 'Feminino',
                    'O' => 'Outro',
            ], ['prompt' => 'Selecione o género...', 'class' => 'form-select']) ?>
        </div>
        <div class="col-md-9">
            <?= $form->field($model, 'morada')->textInput(['placeholder' => 'Morada completa']) ?>
        </div>
    </div>

    <!-- 🔹 Dropdown Role -->
    <?php if (Yii::$app->user->can('admin')): ?>
        <div class="mt-4">
            <h6 class="fw-bold text-secondary mb-2">
                <i class="bi bi-shield-lock-fill text-success me-1"></i> Função / Role
            </h6>

            <div class="card border-success mt-4">
                <div class="card-body">
                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-shield-lock-fill me-1"></i> Função / Role</h6>
                    <?= $form->field($model, 'role')->dropDownList(
                            $roleOptions,
                            [
                                    'prompt' => 'Selecione uma função...',
                                    'class' => 'form-select border-success shadow-sm'
                            ]
                    )->label(false) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Botões -->
    <div class="form-group text-end mt-4">
        <?= Html::submitButton(
                $model->isNewRecord
                        ? '<i class="bi bi-check-circle me-1"></i> Criar'
                        : '<i class="bi bi-save2-fill me-1"></i> Guardar',
                ['class' => 'btn btn-success px-4']
        ) ?>
        <?= Html::a('<i class="bi bi-x-circle me-1"></i> Cancelar', ['index'], [
                'class' => 'btn btn-outline-secondary px-3'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
