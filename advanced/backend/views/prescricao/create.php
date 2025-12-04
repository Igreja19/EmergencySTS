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
