<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/app/bootstrap.php';

$failures = [];

$create = new ReflectionMethod('Poem', 'create');
$createParams = $create->getParameters();
if (count($createParams) < 8 || $createParams[7]->getType() === null || (string) $createParams[7]->getType() !== 'array') {
    $failures[] = 'Poem::create should accept a final array of volume ids.';
}

$update = new ReflectionMethod('Poem', 'update');
$updateParams = $update->getParameters();
if (count($updateParams) < 8 || $updateParams[7]->getType() === null || (string) $updateParams[7]->getType() !== 'array') {
    $failures[] = 'Poem::update should accept a final array of volume ids.';
}

if (!method_exists('Poem', 'volumesFor')) {
    $failures[] = 'Poem::volumesFor should exist for viewing assigned volumes.';
}

if (count($failures) > 0) {
    foreach ($failures as $failure) {
        echo "FAIL: {$failure}\n";
    }
    exit(1);
}

echo "PASS: poem volume assignment API is present\n";
exit(0);
