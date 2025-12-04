<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Triagem $model */
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/triagem/view.css');

$this->title = 'Detalhes da Triagem';
$this->params['breadcrumbs'][] = ['label' => 'Triagens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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

    <div class="d-flex justify-content-center gap-3 mt-4 butoes">

        <?php $fromPulseira = Yii::$app->request->get('pulseira_id'); ?>

        <div class="text-center mt-3 butao">

            <?= Html::a(
                    '<i class="bi bi-arrow-left-circle me-1"></i> Voltar',
                    $fromPulseira
                            ? ['pulseira/update', 'id' => $fromPulseira]
                            : ['index'],
                    ['class' => 'btn btn-voltar']
            ) ?>

            <?= Html::a(
                    '<i class="bi bi-pencil-square me-1"></i> Editar',
                    $fromPulseira
                            ? ['triagem/update', 'id' => $model->id, 'fromPulseira' => $fromPulseira]
                            : ['triagem/update', 'id' => $model->id],
                    ['class' => 'btn btn-editar']
            ) ?>

        </div>

    </div>

</div>
