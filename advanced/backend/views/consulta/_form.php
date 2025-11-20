<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Consulta $model */
/** @var array $triagensDisponiveis */

$triagensDisponiveis = $triagensDisponiveis ?? [];
$isNew = $model->isNewRecord;
?>

<div class="consulta-box p-4 shadow-sm rounded-4 bg-white">

    <h3 class="text-success fw-bold mb-4 d-flex align-items-center">
        <i class="bi bi-clipboard2-pulse me-2"></i>
        <?= $isNew ? 'Criar Consulta' : 'Editar Consulta' ?>
    </h3>

    <?php $form = ActiveForm::begin(); ?>

    <!-- ===================== -->
    <!-- DADOS PRINCIPAIS -->
    <!-- ===================== -->
    <div class="section-title mb-3">
        <i class="bi bi-info-circle-fill text-success me-2"></i>
        <span class="fw-semibold text-success">Informações da Consulta</span>
    </div>

    <div class="row g-3">

        <div class="col-md-6">
            <?= $form->field($model, 'estado')->dropDownList([
                    'Em curso' => 'Em curso',
                    'Encerrada' => 'Encerrada'
            ], [
                    'class' => 'form-select rounded-3 shadow-sm',
                    'disabled' => $isNew,
                    'id' => 'estado-select'
            ]) ?>
        </div>

        <div class="col-md-6" id="campo-encerramento"
             style="<?= $model->estado === 'Encerrada' ? '' : 'display:none;' ?>">

            <?= $form->field($model, 'data_encerramento')
                    ->input('datetime-local', [
                            'class' => 'form-control rounded-3 shadow-sm',
                            'value' => $model->data_encerramento
                                    ? date('Y-m-d\TH:i', strtotime($model->data_encerramento))
                                    : null
                    ]) ?>
        </div>

    </div>

    <!-- ===================== -->
    <!-- TRIAGEM + PACIENTE -->
    <!-- ===================== -->
    <div class="section-title mt-4 mb-3">
        <i class="bi bi-person-fill text-success me-2"></i>
        <span class="fw-semibold text-success">Dados do Paciente</span>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <?= $form->field($model, 'triagem_id')->dropDownList(
                    $triagensDisponiveis,
                    [
                            'prompt' => '— Selecione a Triagem (Pulseira) —',
                            'class' => 'form-select rounded-3 shadow-sm',
                            'id' => 'triagem-select',
                            'disabled' => !$isNew
                    ]
            ) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'userprofile_id')
                    ->hiddenInput(['id' => 'userprofile-id'])
                    ->label(false) ?>

            <label class="form-label fw-semibold text-success">Paciente</label>
            <input type="text"
                   id="userprofile-nome"
                   class="form-control rounded-3 shadow-sm"
                   value="<?= $model->userprofile->nome ?? '' ?>"
                   placeholder="Preenchido automaticamente"
                   readonly>
        </div>
    </div>

    <!-- ===================== -->
    <!-- OBSERVAÇÕES -->
    <!-- ===================== -->
    <div class="section-title mt-4 mb-3">
        <i class="bi bi-journal-text text-success me-2"></i>
        <span class="fw-semibold text-success">Observações</span>
    </div>

    <?= $form->field($model, 'observacoes')->textarea([
            'rows' => 4,
            'class' => 'form-control rounded-3 shadow-sm',
            'placeholder' => 'Registe aqui notas importantes...'
    ])->label(false) ?>

    <!-- BOTÕES -->
    <div class="d-flex justify-content-end mt-4 gap-2">
        <?= Html::submitButton(
                '<i class="bi bi-check2-circle me-1"></i>Guardar',
                ['class' => 'btn btn-success px-4 rounded-3 fw-semibold']
        ) ?>

        <?= Html::a(
                '<i class="bi bi-x-circle me-1"></i>Cancelar',
                ['index'],
                ['class' => 'btn btn-outline-secondary px-4 rounded-3 fw-semibold']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$triagemInfoUrl = Url::to(['consulta/triagem-info']);
$js = <<<JS

// AJAX para preencher nome do paciente
$('#triagem-select').on('change', function() {
    let triagemId = $(this).val();

    if (!triagemId) {
        $('#userprofile-id').val('');
        $('#userprofile-nome').val('');
        return;
    }

    $.get('$triagemInfoUrl', {id: triagemId}, function(data) {
        $('#userprofile-id').val(data.userprofile_id || '');
        $('#userprofile-nome').val(data.user_nome || '');
    });
});

// Mostrar/esconder campo de encerramento
$('#estado-select').on('change', function() {
    if ($(this).val() === 'Encerrada') {
        $('#campo-encerramento').slideDown();
    } else {
        $('#campo-encerramento').slideUp();
        $('#consulta-data_encerramento').val('');
    }
});
JS;

$this->registerJs($js);
?>
