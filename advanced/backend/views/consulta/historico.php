<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $medicos \common\models\UserProfile[] */
/** @var $consultas \common\models\Consulta[] */

$this->title = "Histórico de Consultas";

?>

<div class="card shadow p-4">

    <h3 class="text-success fw-bold mb-3">
        <i class="bi bi-clock-history"></i> Histórico de Consultas
    </h3>

    <!-- FILTRO DE MÉDICO -->
    <form method="get" class="mb-3">
        <label class="form-label fw-semibold">Filtrar por Médico:</label>
        <select name="medico" class="form-select" onchange="this.form.submit()">
            <option value="">— Todos —</option>
            <?php foreach ($medicos as $m): ?>
                <option value="<?= $m->id ?>"
                    <?= Yii::$app->request->get('medico') == $m->id ? 'selected' : '' ?>>
                    <?= $m->nome ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Paciente</th>
            <th>Médico</th>
            <th>Data Consulta</th>
            <th>Encerramento</th>
            <th>Ações</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($consultas as $c): ?>
            <tr>
                <td><?= $c->id ?></td>
                <td><?= $c->userprofile->nome ?></td>
                <td><?= $c->triagem->userprofile->nome ?></td>
                <td><?= date('d/m/Y H:i', strtotime($c->data_consulta)) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($c->data_encerramento)) ?></td>
                <td>
                    <?= Html::a('Ver', ['view', 'id' => $c->id], ['class'=>'btn btn-success btn-sm']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
