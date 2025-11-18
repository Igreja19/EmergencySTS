<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\Pulseira $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/pulseira/_form.css');
?>

<div class="pulseira-form">

    <?php $form = ActiveForm::begin(); ?>

    <h5><i class="bi bi-upc me-2"></i> Dados da Pulseira</h5>

    <div class="row g-3 mb-3">

        <?php if ($model->isNewRecord): ?>

            <!-- 🔹 Selecionar pulseira sem prioridade -->
            <div class="col-md-6">
                <?= $form->field($model, 'codigo')->dropDownList(
                        ArrayHelper::map(
                                \common\models\Pulseira::find()
                                        ->where(['or',
                                                ['prioridade' => null],
                                                ['prioridade' => '']
                                        ])
                                        ->all(),
                                'codigo',
                                fn($p) => "Código: {$p->codigo} — ID #{$p->id}"
                        ),
                        ['prompt' => '— Selecionar Pulseira —']
                ) ?>
            </div>

            <!-- 🔹 Selecionar utilizador da pulseira -->
            <div class="col-md-6">
                <?= $form->field($model, 'userprofile_id')->dropDownList(
                        ArrayHelper::map(
                                \common\models\UserProfile::find()->all(),
                                'id',
                                fn($u) => "{$u->nome} (ID {$u->id})"
                        ),
                        ['prompt' => '— Selecionar Utente —']
                ) ?>
            </div>

        <?php else: ?>

            <!-- 🔹 Código mostrado no update -->
            <div class="col-md-6">
                <?= $form->field($model, 'codigo')->textInput([
                        'readonly' => true,
                        'class' => 'form-control-plaintext fw-bold'
                ]) ?>
            </div>

            <!-- 🔹 Utilizador (bloqueado) -->
            <div class="col-md-6">
                <?= $form->field($model, 'userprofile_id')->textInput([
                        'readonly' => true,
                        'value' => $model->userprofile->nome,
                        'class' => 'form-control-plaintext fw-bold'
                ]) ?>
            </div>

        <?php endif; ?>

        <!-- 🔹 Selecionar prioridade -->
        <div class="col-md-6">
            <?= $form->field($model, 'prioridade')->dropDownList([
                    'Vermelho' => 'Vermelho',
                    'Laranja'  => 'Laranja',
                    'Amarelo'  => 'Amarelo',
                    'Verde'    => 'Verde',
                    'Azul'     => 'Azul',
            ], ['prompt' => 'Selecione a prioridade...']) ?>
        </div>

        <!-- 🔹 Estado -->
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList([
                    'Em espera'        => 'Em espera',
                    'Em atendimento'   => 'Em atendimento',
                    'Atendido'         => 'Atendido',
            ], ['prompt' => 'Selecionar estado...']) ?>
        </div>

    </div>

    <div class="text-center mt-4">
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Guardar', ['class' => 'btn btn-save']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
