<?php
$files = [
    'common/config/main.php',
    'common/config/main-local.php',
    'common/config/test.php',
    'common/config/test-local.php'
];

echo "--- INICIO DO DIAGNOSTICO ---\n";

foreach ($files as $file) {
    echo "A verificar: $file ... ";

    if (!file_exists($file)) {
        echo "NAO EXISTE (Isto pode ser o problema se for esperado)\n";
        continue;
    }

    $content = include $file;

    if (is_array($content)) {
        echo "OK (Array)\n";
    } else {
        echo "ERRO !!! -> Devolveu " . gettype($content) . " em vez de Array.\n";
        echo "CORRIGA ESTE FICHEIRO!\n";
    }
}
echo "--- FIM ---\n";