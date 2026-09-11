<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/app/bootstrap.php';

if (!method_exists('PoemController', 'index')) {
    echo "FAIL: PoemController::index is missing\n";
    exit(1);
}

if (!file_exists(__DIR__ . '/../src/poems/index.php')) {
    echo "FAIL: poems index route is missing\n";
    exit(1);
}

echo "PASS: poem index route exists\n";
exit(0);
