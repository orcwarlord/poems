<?php
/**
 * Shared page header.
 *
 * Expected variables set by the calling page:
 *   string $pageTitle   — used in <title>
 *   bool   $useEditor   — set true to load Quill CSS in <head>
 */

declare(strict_types=1);

$_success = get_flash('success');
$_error   = get_flash('error');
?>
<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> — Verse Library</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN (includes typography plugin) -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                        display: ['"Josefin Sans"', 'sans-serif'],
                    },
                    typography: {
                        DEFAULT: {
                            css: {
                                '--tw-prose-body':        '#303030',
                                '--tw-prose-headings':    '#303030',
                                '--tw-prose-links':       '#3a7b3a',
                                '--tw-prose-bold':        '#303030',
                                '--tw-prose-blockquotes': '#888888',
                                maxWidth: 'none',
                            },
                        },
                    },
                },
            },
        };
    </script>

    <?php if (!empty($useEditor)): ?>
    <!-- Quill rich-text editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <?php endif; ?>

    <link rel="stylesheet" href="/assets/app.css">
</head>
<body class="min-h-screen bg-[#fffff0] font-sans text-stone-900 antialiased">

<!-- ── Navigation ──────────────────────────────────────────────────────────── -->
<nav class="sticky top-0 z-20 border-b border-stone-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
    <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-6 lg:px-10">
        <a href="/" class="flex items-center gap-2 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
            </svg>
            <span class="font-display text-lg font-semibold text-stone-800 group-hover:text-amber-700 transition-colors">Verse Library</span>
        </a>

        <a href="/poems/create.php"
           class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
            </svg>
            New Poem
        </a>
        <a href="/images/" class="text-sm font-semibold text-stone-600 hover:text-amber-700">Manage Images</a>
    </div>
</nav>

<!-- ── Flash messages ──────────────────────────────────────────────────────── -->
<?php if ($_success): ?>
<div class="border-b border-green-200 bg-green-50 px-6 py-3 text-center text-sm font-medium text-green-800" role="alert">
    <?= e($_success) ?>
</div>
<?php endif; ?>
<?php if ($_error): ?>
<div class="border-b border-red-200 bg-red-50 px-6 py-3 text-center text-sm font-medium text-red-800" role="alert">
    <?= e($_error) ?>
</div>
<?php endif; ?>

<!-- ── Page content ────────────────────────────────────────────────────────── -->
<main class="mx-auto max-w-6xl px-6 py-10 lg:px-10">
