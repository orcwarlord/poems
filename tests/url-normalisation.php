<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/app/bootstrap.php';

$cases = [
    'do.com' => 'https://do.com',
    'www.example.com' => 'https://www.example.com',
    'https://example.com' => 'https://example.com',
    'http://example.com' => 'http://example.com',
    'example.com/path' => 'https://example.com/path',
    '/relative/path' => '/relative/path',
    '' => '',
];

$failures = 0;
foreach ($cases as $input => $expected) {
    $actual = normalise_url_value($input);
    if ($actual !== $expected) {
        echo "FAIL: input={$input} expected={$expected} actual={$actual}\n";
        $failures++;
    }
}

if ($failures === 0) {
    echo "PASS: all URL normalisation checks succeeded\n";
    exit(0);
}

exit(1);
