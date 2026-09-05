<?php

declare(strict_types=1);

function render(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../views/' . $template . '.php';
}
