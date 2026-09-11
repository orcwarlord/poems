<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/app/bootstrap.php';

if (!class_exists('Volume')) {
    echo "FAIL: Volume model missing\n";
    exit(1);
}

echo "PASS: Volume model exists\n";
exit(0);
