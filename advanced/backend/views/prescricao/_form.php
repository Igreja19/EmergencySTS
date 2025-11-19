<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */
/** @var array $consultas */
/** @var array $medicamentosDropdown */
/** @var common\models\Prescricaomedicamento[] $prescricaoMedicamentos */

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/prescricao/_form.css');

?>

<?php $form = ActiveForm::begin(); ?>

<!-- CARD: DADOS GERAIS DA PRESCRIÇÃO -->
<div class="card shadow-sm mb-4" style="border-radius: 12px;">
    <div class="p-3 text-white"
         style="background: #1f9d55; border-radius: 12px 12px 0 0;">
        <h5 class="m-0">
            <i class="bi bi-file-earmark-medical me-2"></i>
            Dados da Prescrição
        </h5>
    </div>

    <div class="card-body">

        <div class="mb-3">
            <?= $form->field($model, 'observacoes')->textarea([
                    'rows' => 3,
                    'class' => 'form-control shadow-sm'
            ]) ?>
        </div>

        <div class="mb-3">
            <?= $form->field($model, 'consulta_id')->dropDownList(
                    $consultas,                                // ✔ agora correto
                    [
                            'class' => 'form-select shadow-sm',
                            'prompt' => 'Selecione uma consulta...'
                    ]
            ) ?>
        </div>

    </div>
</div>

<!-- CARD: MEDICAMENTOS -->
<div class="card shadow-sm" style="border-radius: 12px;">
    <div class="p-3 text-white d-flex justify-content-between align-items-center"
         style="background: #1f9d55; border-radius: 12px 12px 0 0;">

        <h5 class="m-0">
            <i class="bi bi-capsule me-2"></i> Medicamentos
        </h5>

        <button type="button" id="add-medicamento" class="btn btn-light text-success fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Adicionar Medicamento
        </button>
    </div>

    <div class="card-body">

        <div id="medicamentos-container">

            <?php foreach ($prescricaoMedicamentos as $i => $pm): ?>
                <div class="row g-3 medicamento-item border rounded p-3 mb-3 shadow-sm">

                    <div class="col-md-5">
                        <label class="form-label fw-bold text-secondary">Medicamento</label>
                        <?= Html::dropDownList(
                                "Prescricaomedicamento[$i][medicamento_id]",
                                $pm->medicamento_id,
                                $medicamentosDropdown,
                                ['class' => 'form-select shadow-sm', 'required' => true]
                        ) ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">Posologia</label>
                        <?= Html::textInput(
                                "Prescricaomedicamento[$i][posologia]",
                                $pm->posologia,
                                [
                                        'class' => 'form-control shadow-sm',
                                        'placeholder' => 'Ex: 1 comprimido 2x ao dia',
                                        'required' => true
                                ]
                        ) ?>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remover w-100 shadow-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    <?= Html::hiddenInput("Prescricaomedicamento[$i][id]", $pm->id); ?>
                </div>
            <?php endforeach; ?>

        </div>

    </div>
</div>

<!-- BOTÕES FINAIS -->
<div class="d-flex justify-content-end mt-4 gap-2">
    <?= Html::a(
            '<i class="bi bi-arrow-left-circle"></i> Cancelar',
            ['index'],
            ['class' => 'btn btn-outline-secondary btn-lg px-4 shadow-sm']
    ) ?>

    <?= Html::submitButton(
            $model->isNewRecord
                    ? '<i class="bi bi-check-circle"></i> Criar Prescrição'
                    : '<i class="bi bi-save"></i> Guardar Alterações',
            ['class' => 'btn btn-success btn-lg px-4 shadow-sm']
    ) ?>
</div>

<?php ActiveForm::end(); ?>

<!-- JAVASCRIPT DOS CAMPOS DINÂMICOS -->
<script>
    let index = <?= count($prescricaoMedicamentos) ?>;

    document.getElementById('add-medicamento').addEventListener('click', function () {

        let container = document.getElementById('medicamentos-container');

        let html = `
        <div class="row g-3 medicamento-item border rounded p-3 mb-3 shadow-sm">

            <div class="col-md-5">
                <label class="form-label fw-bold text-secondary">Medicamento</label>
                <select class="form-select shadow-sm"
                        name="Prescricaomedicamento[${index}][medicamento_id]" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($medicamentosDropdown as $id => $nome): ?>
                        <option value="<?= $id ?>"><?= $nome ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Posologia</label>
                <input type="text"
                       class="form-control shadow-sm"
                       name="Prescricaomedicamento[${index}][posologia]"
                       placeholder="Ex: 1 comprimido 2x ao dia"
                       required>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger remover w-100 shadow-sm">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

        </div>
    `;

        container.insertAdjacentHTML('beforeend', html);
        index++;
    });

    // Remover itens
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remover')) {
            e.target.closest('.medicamento-item').remove();
        }
    });
</script>
