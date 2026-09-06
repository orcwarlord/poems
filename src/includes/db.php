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
            $columnExists = $pdo->query(
                "SELECT COUNT(*) FROM information_schema.columns
                 WHERE table_schema = DATABASE() AND table_name = 'poems' AND column_name = 'description'"
            )->fetchColumn();
            if ((int) $columnExists === 0) {
                $pdo->exec('ALTER TABLE poems ADD COLUMN description MEDIUMTEXT NOT NULL AFTER title');
            }
            $pdo->exec(
                'CREATE TABLE IF NOT EXISTS photo_assets (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    original_path VARCHAR(255) NOT NULL,
                    thumbnail_path VARCHAR(255) NOT NULL,
                    original_name VARCHAR(255) NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    alt_text VARCHAR(255) NOT NULL,
                    is_default BOOLEAN NOT NULL DEFAULT FALSE,
                    mime_type VARCHAR(50) NOT NULL,
                    width INT UNSIGNED NOT NULL,
                    height INT UNSIGNED NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
            );
            foreach (['title', 'alt_text', 'is_default'] as $photoColumn) {
                $photoColumnExists = $pdo->prepare(
                    "SELECT COUNT(*) FROM information_schema.columns
                     WHERE table_schema = DATABASE() AND table_name = 'photo_assets' AND column_name = :column_name"
                );
                $photoColumnExists->execute(['column_name' => $photoColumn]);
                if ((int) $photoColumnExists->fetchColumn() === 0) {
                    $definition = $photoColumn === 'is_default'
                        ? 'BOOLEAN NOT NULL DEFAULT FALSE'
                        : "VARCHAR(255) NOT NULL DEFAULT ''";
                    $pdo->exec("ALTER TABLE photo_assets ADD COLUMN {$photoColumn} {$definition} AFTER original_name");
                }
            }
            $photoColumnExists = $pdo->query(
                "SELECT COUNT(*) FROM information_schema.columns
                 WHERE table_schema = DATABASE() AND table_name = 'poems' AND column_name = 'photo_id'"
            )->fetchColumn();
            if ((int) $photoColumnExists === 0) {
                $pdo->exec('ALTER TABLE poems ADD COLUMN photo_id INT UNSIGNED NULL AFTER content');
            }
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
