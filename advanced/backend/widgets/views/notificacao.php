<?php
use yii\helpers\Url;
?>

<li class="nav-item dropdown">
    <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
        <i class="far fa-bell"></i>

        <?php if ($totalNaoLidas > 0): ?>
            <span class="badge bg-danger navbar-badge"><?= $totalNaoLidas ?></span>
        <?php endif; ?>
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">

        <span class="dropdown-header">
            <?= $totalNaoLidas ?> Notificações
        </span>

        <div class="dropdown-divider"></div>

        <?php if (empty($naoLidas)): ?>
            <p class="text-center text-muted p-3">Sem notificações</p>
        <?php else: ?>
            <?php foreach ($naoLidas as $n): ?>
                <a href="<?= Url::to(['/notificacao/lida', 'id' => $n->id]) ?>" class="dropdown-item">
                    <strong><?= $n->titulo ?></strong>
                    <br>
                    <span class="text-muted small"><?= $n->mensagem ?></span>
                </a>
                <div class="dropdown-divider"></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="<?= Url::to(['/notificacao']) ?>" class="dropdown-item dropdown-footer">
            Ver todas as notificações
        </a>
    </div>
</li>
