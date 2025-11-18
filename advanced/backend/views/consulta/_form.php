<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;


$this->registerCssFile(Yii::$app->request->baseUrl . '/css/consulta/_form.css');

/** @var yii\web\View $this */
/** @var common\models\Consulta $model */
/** @var array $triagensDisponiveis */

$isNew = $model->isNewRecord;
?>

<div class="consulta-form card shadow-sm border-0 p-4 rounded-4">

    <?php $form = ActiveForm::begin(); ?>

    <!-- TÍTULO E DATA VISUAL -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-success d-flex align-items-center">
            <i class="bi bi-calendar2-heart me-2 fs-5"></i>
            <?= $isNew ? "Criar Consulta" : "Editar Consulta" ?>
        </h5>

        <!-- BADGE DA DATA DA CONSULTA -->
        <span class="badge bg-light border text-dark px-3 py-2 shadow-sm">
            <i class="bi bi-clock-history me-1 text-success"></i>
            <?= $model->data_consulta ? date('d/m/Y H:i', strtotime($model->data_consulta)) : 'A definir...' ?>
        </span>
    </div>

    <!-- Campo hidden (necessário para o POST funcionar) -->
    <?= $form->field($model, 'data_consulta')->hiddenInput()->label(false) ?>

    <!-- ===================== -->
    <!-- ESTADO + ENCERRAMENTO -->
    <!-- ===================== -->
    <div class="section-box p-3 rounded-3 mb-3">
        <h6 class="text-success fw-bold mb-3">
            <i class="bi bi-clipboard2-pulse me-1"></i> Estado da Consulta
        </h6>

        <div class="row g-3">
            <div class="col-md-6">
                <?= $form->field($model, 'estado')
                        ->dropDownList(
                                [
                                        'Em curso' => 'Em curso',
                                        'Encerrada' => 'Encerrada',
                                ],
                                [
                                        'class' => 'form-select rounded-3 shadow-sm',
                                        'disabled' => $isNew,
                                        'id' => 'estado-select'
                                ]
                        )
                        ->label('Estado Atual') ?>
            </div>

            <div class="col-md-6" id="campo-encerramento"
                 style="<?= $model->estado === 'Encerrada' ? '' : 'display:none;' ?>">
                <?= $form->field($model, 'data_encerramento')
                        ->input('datetime-local', [
                                'class' => 'form-control rounded-3 shadow-sm',
                        ])
                        ->label('Data de Encerramento') ?>
            </div>
        </div>
    </div>

    <!-- ===================== -->
    <!-- TRIAGEM E PACIENTE   -->
    <!-- ===================== -->
    <div class="section-box p-3 rounded-3 mb-3">
        <h6 class="text-success fw-bold mb-3">
            <i class="bi bi-person-lines-fill me-1"></i> Dados do Paciente
        </h6>

        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <?= $form->field($model, 'triagem_id')
                        ->dropDownList(
                                $triagensDisponiveis,
                                [
                                        'prompt' => '— Selecione a Triagem (Pulseira) —',
                                        'id' => 'triagem-select',
                                        'class' => 'form-select rounded-3 shadow-sm',
                                        'disabled' => !$isNew
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
                       value="<?= $model->userprofile->nome ?? '' ?>"
                       placeholder="Será preenchido automaticamente..."
                       readonly>
            </div>
        </div>
    </div>

    <!-- ===================== -->
    <!-- OBSERVAÇÕES          -->
    <!-- ===================== -->
    <div class="section-box p-3 rounded-3 mb-3">
        <h6 class="text-success fw-bold mb-3">
            <i class="bi bi-journal-text me-1"></i> Observações
        </h6>

        <?= $form->field($model, 'observacoes')
                ->textarea([
                        'rows' => 4,
                        'placeholder' => 'Registe aqui notas importantes...',
                        'class' => 'form-control rounded-3 shadow-sm'
                ])
                ->label(false) ?>
    </div>

    <!-- BOTÕES -->
    <div class="mt-4 d-flex justify-content-end gap-2">
        <?= Html::submitButton(
                '<i class="bi bi-save2 me-1"></i> Guardar',
                ['class' => 'btn btn-save px-4']
        ) ?>

        <?= Html::a(
                '<i class="bi bi-x-circle me-1"></i> Cancelar',
                ['index'],
                ['class' => 'btn btn-cancel px-4']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/consulta/_form.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("window.triagemInfoUrl = '" . Url::to(['consulta/triagem-info']) . "';");
?>
