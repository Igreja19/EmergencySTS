<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var common\models\Userprofile $model */
$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Utilizadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="m-0"><?= Html::encode($this->title) ?></h3>
            <div>
                <?= Html::a('<i class="fas fa-edit mr-1"></i> Editar', ['update', 'id' => $model->id], ['class'=>'btn btn-primary']) ?>
                <?= Html::a('<i class="fas fa-trash mr-1"></i> Eliminar', ['delete', 'id' => $model->id], [
                        'class'=>'btn btn-outline-danger',
                        'data-confirm'=>'Eliminar este registo?',
                        'data-method'=>'post'
                ]) ?>
            </div>
        </div>

        <?= DetailView::widget([
                'model' => $model,
                'options' => ['class'=>'table table-striped'],
                'attributes' => [
                        'nome',
                        'nif',
                        ['attribute'=>'datanascimento','format'=>['date','php:d/m/Y'],'label'=>'Data de nascimento'],
                        'genero',
                        'sns',
                        'telefone',
                        'email:email',
                        'morada',
                        'observacoes:ntext',
                ],
        ]) ?>
    </div>
</div>
