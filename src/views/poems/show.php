<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-amber-700 transition-colors">&larr; Back to library</a>
</nav>

<article class="mx-auto max-w-3xl">
    <header class="mb-8 border-b border-stone-200 pb-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Poem</p>
            <time datetime="<?= e($poem['created_at']) ?>" class="text-sm text-stone-400">Created <?= date('F j, Y', strtotime($poem['created_at'])) ?></time>
        </div>
        <h1 class="mt-3 font-display text-4xl leading-tight text-stone-900 sm:text-5xl"><?= e($poem['title']) ?></h1>
        <?php if ($poem['updated_at'] !== $poem['created_at']): ?>
        <p class="mt-2 text-xs text-stone-400">Updated <?= date('M j, Y', strtotime($poem['updated_at'])) ?></p>
        <?php endif; ?>
    </header>

    <?php if ($poem['categories'] !== []): ?>
    <div class="mb-8 flex flex-wrap gap-2">
        <?php foreach ($poem['categories'] as $category): ?>
        <a href="/categories/view.php?id=<?= $category['id'] ?>" class="rounded-full border border-stone-200 px-3 py-1 text-sm text-stone-600 hover:border-amber-300 hover:text-amber-700 transition-colors"><?= e($category['name']) ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="poem-content prose prose-stone max-w-none"><?= sanitize_html($poem['content']) ?></div>

    <footer class="mt-10 flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
        <a href="/poems/edit.php?id=<?= $poem['id'] ?>" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Edit</a>
        <form method="post" action="/poems/delete.php" onsubmit="return confirm('Delete this poem? This cannot be undone.')">
            <input type="hidden" name="id" value="<?= $poem['id'] ?>">
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Delete</button>
        </form>
    </footer>
</article>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
