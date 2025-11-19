<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Prescricao $model */
/** @var array $consultas */
/** @var array $medicamentosDropdown */
/** @var common\models\Prescricaomedicamento[] $prescricaoMedicamentos */
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/prescricao/create.css');

$this->title = 'Nova Prescrição';
$this->params['breadcrumbs'][] = ['label' => 'Prescrições', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="prescricao-create">

    <!-- TÍTULO DA PÁGINA -->
    <h1 class="page-title"><?= Html::encode($this->title) ?></h1>

    <!-- HEADER VERDE -->
    <div class="card shadow-sm mb-4 p-0" style="border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center p-3"
             style="background: #1f9d55; border-radius: 12px 12px 0 0;">
            <h4 class="text-white m-0">
                <i class="bi bi-file-earmark-medical-fill me-2"></i> Criar Prescrição
            </h4>

            <?= Html::a('<i class="bi bi-arrow-left-circle"></i> Voltar',
                    ['index'],
                    ['class' => 'btn btn-light fw-bold']
            ) ?>
        </div>

        <!-- FORMULÁRIO (renderiza _form.php) -->
        <div class="p-4">
            <?= $this->render('_form', [
                    'model' => $model,
                    'consultas' => $consultas,
                    'medicamentosDropdown' => $medicamentosDropdown,
                    'prescricaoMedicamentos' => $prescricaoMedicamentos,
            ]) ?>
        </div>
    </div>

</div>
