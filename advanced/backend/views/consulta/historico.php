<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

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

    <div class="card shadow-sm p-3" style="border-radius:12px;">

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
                'dataProvider'  => $dataProvider,
                'filterModel'   => null,
                'summary'       => '<small>Mostrando <b>{count}</b> de <b>{totalCount}</b> consultas.</small>',
                'tableOptions'  => ['class' => 'table table-striped align-middle'],
                'headerRowOptions' => ['class' => 'table-light'],

                'columns' => [

                    // ID
                        [
                                'attribute' => 'id',
                                'label' => 'ID',
                                'contentOptions' => ['style' => 'width:60px; font-weight:bold; color:#1f9d55;']
                        ],

                        // Paciente
                            [
                                    'label' => 'Paciente',
                                    'value' => function($model){
                                        return $model->userprofile->nome ?? '-';
                                    }
                            ],

                        // Médico
                            [
                                    'label' => 'Médico',
                                    'value' => function($model){
                                        return $model->medico->nome ?? '-';
                                    }
                            ],

                    // Data da consulta
                        [
                                'label' => 'Data Consulta',
                                'value' => function($model){
                                    return date('d/m/Y H:i', strtotime($model->data_consulta));
                                }
                        ],

                    // Encerramento
                        [
                                'label' => 'Encerramento',
                                'value' => function($model){
                                    return $model->data_encerramento
                                            ? date('d/m/Y H:i', strtotime($model->data_encerramento))
                                            : '-';
                                }
                        ],

                    // AÇÕES
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'contentOptions' => ['style' => 'text-align:center; width:140px;'],
                                'template' => '{view} {delete}',
                                'buttons' => [
                                        'view' => function ($url, $model) {
                                            return Html::a(
                                                    '<i class="bi bi-eye"></i>',
                                                    ['view', 'id' => $model->id],
                                                    ['class'=>'btn btn-success btn-sm']
                                            );
                                        },
                                        'delete' => function ($url) {
                                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'title' => 'Eliminar',
                                                    'data-confirm' => 'Tem a certeza que deseja eliminar esta prescrição?',
                                                    'data-method' => 'post'
                                            ]);
                                        },
                                ],
                        ],

                ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>

</div>
