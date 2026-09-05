<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/view.php';
require_once __DIR__ . '/Models/Category.php';
require_once __DIR__ . '/Models/Poem.php';
require_once __DIR__ . '/Controllers/CategoryController.php';
require_once __DIR__ . '/Controllers/PoemController.php';

function poem_controller(): PoemController
{
    static $controller;

    if ($controller === null) {
        $controller = new PoemController(new Poem(get_db()), new Category(get_db()));
    }

    return $controller;
}

function category_controller(): CategoryController
{
    static $controller;

    if ($controller === null) {
        $controller = new CategoryController(new Category(get_db()));
    }

    return $controller;
}
