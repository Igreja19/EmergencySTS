<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Paciente $model */
/** @var yii\widgets\ActiveForm $form */
?>
<div class="card">
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <div class="form-row">
            <div class="col-md-6">
                <?= $form->field($model, 'nomecompleto')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nif')->textInput(['maxlength' => 9, 'pattern'=>'\d*', 'inputmode'=>'numeric']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'datanascimento')->input('date') ?>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-3">
                <?= $form->field($model, 'genero')->dropDownList([
                        'Masculino'=>'Masculino','Feminino'=>'Feminino','Outro'=>'Outro'
                ], ['prompt' => 'Selecione...']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'sns')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'telefone')->textInput(['maxlength' => true, 'pattern'=>'\d*', 'inputmode'=>'tel']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'email')->input('email') ?>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-12">
                <?= $form->field($model, 'morada')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <?= $form->field($model, 'observacoes')->textarea(['rows' => 4]) ?>

        <div class="form-group mb-0">
            <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Guardar', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancelar', ['index'], ['class'=>'btn btn-light']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
