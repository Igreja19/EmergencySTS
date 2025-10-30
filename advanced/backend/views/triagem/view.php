<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */

$this->title = 'Detalhes da Triagem';
$this->params['breadcrumbs'][] = ['label' => 'Triagens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
.triagem-view {
  max-width: 1000px;
  margin: 0 auto;
}
.triagem-view h1 {
  color: #198754;
  font-weight: 700;
  margin-bottom: 20px;
  text-align: center;
}
.card {
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  background: #fff;
  border: none;
  padding: 25px;
}
.table-details {
  width: 100%;
}
.table-details th {
  background: #198754;
  color: #fff;
  width: 30%;
  text-align: left;
  vertical-align: middle;
  padding: 10px 14px;
  border-bottom: 1px solid #e9ecef;
}
.table-details td {
  background: #f8f9fa;
  color: #333;
  vertical-align: middle;
  padding: 10px 14px;
  border-bottom: 1px solid #e9ecef;
}
.btn-back {
  background: #198754;
  color: #fff;
  border-radius: 10px;
  padding: 10px 20px;
  font-weight: 600;
  transition: .2s;
}
.btn-back:hover { opacity: .9; transform: translateY(-2px); }
.badge-prio {
  padding: 6px 10px;
  border-radius: 8px;
  font-weight: 600;
  color: #fff;
}
.badge-Vermelho { background-color: #dc3545; }
.badge-Laranja  { background-color: #fd7e14; }
.badge-Amarelo  { background-color: #ffc107; color:#000; }
.badge-Verde    { background-color: #198754; }
.badge-Azul     { background-color: #0d6efd; }
');
?>

<div class="triagem-view">

    <h1><i class="bi bi-file-medical me-2"></i><?= Html::encode($this->title) ?></h1>

    <div class="card mb-4">
        <table class="table-details">
            <tr>
                <th>ID</th>
                <td><?= Html::encode($model->id) ?></td>
            </tr>
            <tr>
                <th>Paciente</th>
                <td><?= Html::encode($model->userprofile->nome ?? '—') ?></td>
            </tr>
            <tr>
                <th>Código da Pulseira</th>
                <td><?= Html::encode($model->pulseira->codigo ?? '—') ?></td>
            </tr>
            <tr>
                <th>Prioridade</th>
                <td>
                    <?php $prio = $model->pulseira->prioridade ?? '-'; ?>
                    <?= $prio !== '-' ? "<span class='badge-prio badge-{$prio}'>{$prio}</span>" : '-' ?>
                </td>
            </tr>
            <tr>
                <th>Motivo da Consulta</th>
                <td><?= nl2br(Html::encode($model->motivoconsulta)) ?></td>
            </tr>
            <tr>
                <th>Queixa Principal</th>
                <td><?= nl2br(Html::encode($model->queixaprincipal)) ?></td>
            </tr>
            <tr>
                <th>Descrição dos Sintomas</th>
                <td><?= nl2br(Html::encode($model->descricaosintomas)) ?></td>
            </tr>
            <tr>
                <th>Início dos Sintomas</th>
                <td><?= Html::encode($model->iniciosintomas) ?></td>
            </tr>
            <tr>
                <th>Intensidade da Dor</th>
                <td><?= Html::encode($model->intensidadedor) ?>/10</td>
            </tr>
            <tr>
                <th>Alergias Conhecidas</th>
                <td><?= nl2br(Html::encode($model->alergias)) ?></td>
            </tr>
            <tr>
                <th>Medicação Atual</th>
                <td><?= nl2br(Html::encode($model->medicacao)) ?></td>
            </tr>
            <tr>
                <th>Data da Triagem</th>
                <td><?= Yii::$app->formatter->asDatetime($model->datatriagem, 'php:d/m/Y H:i') ?></td>
            </tr>
        </table>
    </div>

    <div class="text-center">
        <?= Html::a('<i class="bi bi-arrow-left-circle me-1"></i> Voltar', ['index'], ['class' => 'btn btn-back']) ?>
    </div>
</div>
