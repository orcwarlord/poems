<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host     = getenv('DB_HOST')     ?: 'db';
        $port     = getenv('DB_PORT')     ?: '3306';
        $dbname   = getenv('DB_NAME')     ?: 'app';
        $user     = getenv('DB_USER')     ?: 'app';
        $password = getenv('DB_PASSWORD') ?: 'app';

        try {
            $pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (Throwable $e) {
            http_response_code(503);
            exit('Database unavailable.');
        }
    }

    return $pdo;
}

/**
 * Store a one-shot flash message in the session.
 */
function flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

/**
 * Read and consume a flash message (returns null if not set).
 */
function get_flash(string $key): ?string
{
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

/**
 * Strip dangerous tags from rich-text HTML, keeping safe formatting tags.
 */
function sanitize_html(string $html): string
{
    return strip_tags(
        $html,
        '<p><br><strong><b><em><i><u><s><ul><ol><li><h1><h2><h3><blockquote><a><pre><code><span>'
    );
}

/**
 * HTML-encode a value for safe output.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
