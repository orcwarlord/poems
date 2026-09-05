<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
category_controller()->show($id === false ? null : $id);