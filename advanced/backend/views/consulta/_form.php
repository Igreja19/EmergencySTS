<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/consulta.css');

/** @var yii\web\View $this */
/** @var common\models\Consulta $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $triagensDisponiveis */
?>

    <div class="consulta-form card shadow-sm border-0 p-4 rounded-4">
        <?php $form = ActiveForm::begin(); ?>

        <h5 class="fw-bold text-success mb-4 d-flex align-items-center">
            <i class="bi bi-calendar2-heart me-2 fs-5"></i> Dados da Consulta
        </h5>

        <!-- Linha 1 -->
        <div class="row g-3">
            <div class="col-md-4">
                <?= $form->field($model, 'data_consulta')
                        ->input('datetime-local', ['class' => 'form-control rounded-3 shadow-sm'])
                        ->label('Data da Consulta') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'estado')
                        ->dropDownList(
                                [
                                        'Aberta' => 'Aberta',
                                        'Em curso' => 'Em curso',
                                        'Encerrada' => 'Encerrada',
                                ],
                                ['prompt' => '— Selecionar Estado —', 'class' => 'form-select rounded-3 shadow-sm']
                        )
                        ->label('Estado da Consulta') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'data_encerramento')
                        ->input('datetime-local', ['class' => 'form-control rounded-3 shadow-sm'])
                        ->label('Data de Encerramento') ?>
            </div>
        </div>

        <!-- Linha 2 -->
        <div class="row g-3 mt-2 align-items-end">
            <div class="col-md-6">
                <?= $form->field($model, 'triagem_id')
                        ->dropDownList(
                                $triagensDisponiveis,
                                [
                                        'prompt' => '— Selecione a Triagem (Pulseira) —',
                                        'id' => 'triagem-select',
                                        'class' => 'form-select rounded-3 shadow-sm'
                                ]
                        )
                        ->label('<i class="bi bi-upc-scan me-1"></i> Triagem (Pulseira)') ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'userprofile_id')
                        ->hiddenInput(['id' => 'userprofile-id'])
                        ->label(false) ?>

                <label for="userprofile-nome" class="form-label fw-semibold text-success mb-1">
                    <i class="bi bi-person-fill me-1"></i> Paciente
                </label>
                <input type="text"
                       id="userprofile-nome"
                       class="form-control rounded-3 shadow-sm"
                       placeholder="Será preenchido automaticamente..."
                       readonly>
            </div>
        </div>

        <!-- Observações -->
        <div class="mt-3">
            <?= $form->field($model, 'observacoes')
                    ->textarea([
                            'rows' => 4,
                            'placeholder' => 'Observações da consulta...',
                            'class' => 'form-control rounded-3 shadow-sm'
                    ])
                    ->label('<i class="bi bi-journal-text me-1"></i> Observações') ?>
        </div>

        <!-- Botões -->
        <div class="mt-4 d-flex justify-content-end gap-2">
            <?= Html::submitButton('<i class="bi bi-save2 me-1"></i> Guardar', ['class' => 'btn btn-save px-4']) ?>
            <?= Html::a('<i class="bi bi-x-circle me-1"></i> Cancelar', ['index'], ['class' => 'btn btn-cancel px-4']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php
// === SCRIPT AJAX PARA PREENCHER O PACIENTE ===
$triagemInfoUrl = Url::to(['consulta/triagem-info']);
$js = <<<JS
$('#triagem-select').on('change', function() {
    var triagemId = $(this).val();
    if (triagemId) {
        $.ajax({
            url: '$triagemInfoUrl',
            data: {id: triagemId},
            success: function(data) {
                if (data && data.user_nome) {
                    $('#userprofile-id').val(data.userprofile_id);
                    $('#userprofile-nome').val(data.user_nome);
                } else {
                    $('#userprofile-id').val('');
                    $('#userprofile-nome').val('');
                }
            }
        });
    } else {
        $('#userprofile-id').val('');
        $('#userprofile-nome').val('');
    }
});
JS;
$this->registerJs($js);