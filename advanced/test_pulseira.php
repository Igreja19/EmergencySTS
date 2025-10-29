<?php
require __DIR__ . '/vendor/autoload.php';

use common\models\Pulseira;

if (class_exists(Pulseira::class)) {
    echo "✅ Classe encontrada: " . Pulseira::class . PHP_EOL;
} else {
    echo "❌ Classe NÃO encontrada" . PHP_EOL;
}