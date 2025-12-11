<?php use yii\helpers\Html;
use yii\helpers\Url;

if (empty($notificacoes)): ?>
    <div class="text-center p-3 text-muted">
        Nenhuma notificação.
    </div>
<?php else: ?>
    <?php foreach ($notificacoes as $n): ?>
        <div class="dropdown-item notif-item <?= $n->lida ? 'read' : 'unread' ?>">

            <div class="d-flex">
                <div class="me-2 mt-1">
                    <i class="bi <?= $n->lida ? 'bi-envelope-open' : 'bi-bell-fill' ?>"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="fw-semibold">
                        <?= Html::encode($n->titulo ?: 'Notificação') ?>
                    </div>
                    <div class="small text-muted">
                        <?= Html::encode($n->mensagem) ?>
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-clock"></i>
                        <?= date('d/m/Y H:i', strtotime($n->dataenvio)) ?>
                    </div>
                </div>

                <?php if (!$n->lida): ?>
                    <a href="#"
                       class="btn btn-sm btn-outline-success marcar-lida"
                       data-id="<?= $n->id ?>"
                       data-url="<?= Url::to(['/notificacao/lida-ajax']) ?>">
                        <i class="bi bi-check2"></i>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    <?php endforeach; ?>
<?php endif; ?>
