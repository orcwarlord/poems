<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/view.php';
require_once __DIR__ . '/Models/Category.php';
require_once __DIR__ . '/Models/Publisher.php';
require_once __DIR__ . '/Models/Photo.php';
require_once __DIR__ . '/Models/Poem.php';
require_once __DIR__ . '/Models/Submission.php';
require_once __DIR__ . '/Models/Volume.php';
require_once __DIR__ . '/Controllers/CategoryController.php';
require_once __DIR__ . '/Controllers/PublisherController.php';
require_once __DIR__ . '/Controllers/PoemController.php';
require_once __DIR__ . '/Controllers/ImageController.php';
require_once __DIR__ . '/Controllers/SubmissionController.php';
require_once __DIR__ . '/Controllers/VolumeController.php';

function normalise_url_value(string $value): string
{
    $trimmed = trim($value);
    if ($trimmed === '') {
        return '';
    }

    $trimmed = preg_replace('/\s+/', '', $trimmed) ?? $trimmed;

    if (preg_match('/^https?:\/\//i', $trimmed) === 1) {
        return $trimmed;
    }

    if (preg_match('/^\/\//', $trimmed) === 1) {
        return 'https:' . $trimmed;
    }

    if (preg_match('/^\//', $trimmed) === 1) {
        return $trimmed;
    }

    if (preg_match('/^www\./i', $trimmed) === 1) {
        return 'https://' . $trimmed;
    }

    return 'https://' . $trimmed;
}

function poem_controller(): PoemController
{
    static $controller;

    if ($controller === null) {
        $controller = new PoemController(
            new Poem(get_db()),
            new Category(get_db()),
            new Photo(get_db()),
            new Submission(get_db()),
            new Publisher(get_db())
        );
    }

    return $controller;
}

function image_controller(): ImageController
{
    static $controller;

    if ($controller === null) {
        $controller = new ImageController(new Photo(get_db()));
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

function publisher_controller(): PublisherController
{
    static $controller;

    if ($controller === null) {
        $controller = new PublisherController(new Publisher(get_db()));
    }

    return $controller;
}

function submission_controller(): SubmissionController
{
    static $controller;

    if ($controller === null) {
        $controller = new SubmissionController(new Submission(get_db()), new Poem(get_db()), new Publisher(get_db()));
    }

    return $controller;
}

function volume_controller(): VolumeController
{
    static $controller;

    if ($controller === null) {
        $controller = new VolumeController(new Volume(get_db()), new Poem(get_db()));
    }

    return $controller;
}
