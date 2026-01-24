<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Notificações';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/notificacao/index.css');

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success">
            <i class="bi bi-bell-fill me-2"></i> Notificações
        </h3>

        <?php if (!empty($naoLidas)): ?>
            <a href="<?= Url::to(['notificacao/ler-todas']) ?>" class="btn-all-read">
                <i class="bi bi-check-all me-1"></i> Marcar todas como lidas
            </a>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <h5 class="fw-bold mb-3 text-success">
            <i class="bi bi-dot"></i> Não Lidas
        </h5>

        <?php if (empty($naoLidas)): ?>
            <p class="text-muted">Nenhuma notificação por ler.</p>
        <?php else: ?>
            <?php foreach ($naoLidas as $n): ?>
                <div class="notif-item unread mb-2">
                    <div class="notif-icon">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>

                    <div class="flex-grow-1">
                        <div class="notif-title"><?= Html::encode($n->titulo) ?></div>
                        <div class="text-muted small"><?= Html::encode($n->mensagem) ?></div>
                        <div class="text-muted small">
                            <i class="bi bi-clock"></i>
                            <?= date("d/m/Y H:i", strtotime($n->dataenvio)) ?>
                        </div>
                    </div>

                    <div class="align-self-center">
                        <a href="<?= Url::to(['notificacao/lida', 'id' => $n->id]) ?>"
                           class="btn btn-sm btn-outline-success">
                            <i class="bi bi-check2"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <hr>

    <div class="mt-4">
        <h5 class="fw-bold mb-3 text-secondary">
            <i class="bi bi-check2-all"></i> Lidas
        </h5>

        <?php if (empty($todas)): ?>
            <p class="text-muted">Ainda não existem notificações.</p>
        <?php else: ?>
            <?php foreach ($todas as $n): ?>
                <?php if ($n->lida == 1): ?>
                    <div class="notif-item read mb-2">
                        <div class="notif-icon">
                            <i class="bi bi-envelope-open"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="notif-title"><?= Html::encode($n->titulo) ?></div>
                            <div class="text-muted small"><?= Html::encode($n->mensagem) ?></div>
                        </div>

                        <div class="text-muted small align-self-center">
                            <?= date("d/m/Y H:i", strtotime($n->dataenvio)) ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
