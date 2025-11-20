<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Notificações';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
.card-notif {
    border-radius:16px;
    background:#fff;
    box-shadow:0 4px 18px rgba(0,0,0,0.06);
    padding:18px;
    transition:0.25s;
}
.card-notif:hover {
    transform:translateY(-3px);
    box-shadow:0 6px 26px rgba(0,0,0,0.10);
}
.notif-item {
    padding:14px;
    border-radius:14px;
    display:flex;
    gap:14px;
    transition:.25s;
}
.notif-item.unread {
    background:#e9f7ef;
    border-left:4px solid #198754;
}
.notif-item.read {
    background:#f8f9fa;
}
.notif-item:hover {
    transform:translateX(4px);
}

.notif-icon {
    font-size:26px;
    color:#198754;
    margin-top:4px;
}

.notif-title {
    font-weight:600;
    margin-bottom:3px;
}

.btn-all-read {
    background:#198754;
    border:none;
    padding:10px 18px;
    border-radius:10px;
    color:#fff;
    font-weight:600;
}
.btn-all-read:hover {
    background:#146c43;
}
");
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

    <!-- 🔵 NOTIFICAÇÕES NÃO LIDAS -->
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

    <!-- 🟣 NOTIFICAÇÕES LIDAS -->
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
