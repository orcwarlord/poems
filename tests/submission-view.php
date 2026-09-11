<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/app/bootstrap.php';

if (!method_exists('SubmissionController', 'show')) {
    echo "FAIL: SubmissionController::show is missing\n";
    exit(1);
}

if (!file_exists(__DIR__ . '/../src/submissions/view.php')) {
    echo "FAIL: submissions view route is missing\n";
    exit(1);
}

if (!file_exists(__DIR__ . '/../src/views/submissions/show.php')) {
    echo "FAIL: submissions show template is missing\n";
    exit(1);
}

echo "PASS: submission detail route exists\n";
exit(0);
