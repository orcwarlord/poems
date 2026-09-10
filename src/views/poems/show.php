<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/">Dashboard</a>
    <span>/</span>
    <a href="/poems/">Poems</a>
    <span>/</span>
    <span aria-current="page"><?= e($poem['title']) ?></span>
</nav>

<article class="mx-auto max-w-3xl">
    <header class="mb-8 border-b border-stone-200 pb-6">
        <img src="<?= e($poem['photo_thumbnail_path'] ?: '/assets/default-poem.svg') ?>" alt="<?= e($poem['photo_alt_text'] ?? 'Default poem image') ?>" title="<?= e($poem['photo_title'] ?? 'Poem image') ?>" class="mb-6 aspect-[2/1] w-full rounded-xl object-cover">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Version <?= $poem['version_number'] ?></p>
            <time datetime="<?= e($poem['written_date']) ?>" class="text-sm text-stone-500">Written <?= date('F j, Y', strtotime($poem['written_date'])) ?></time>
        </div>
        <h1 class="mt-3 font-display text-4xl leading-tight text-stone-900 sm:text-5xl"><?= e($poem['title']) ?></h1>
        <?php if ($poem['updated_at'] !== $poem['created_at']): ?>
        <p class="mt-2 text-xs text-stone-500">Updated <?= date('M j, Y', strtotime($poem['updated_at'])) ?></p>
        <?php endif; ?>
        <?php if ($poem['parent_poem_id'] !== null): ?>
        <p class="mt-3 text-sm text-stone-500">Version of <a href="/poems/view.php?id=<?= $poem['parent_poem_id'] ?>" class="font-medium text-amber-700 hover:text-amber-800"><?= e($poem['parent_title']) ?></a></p>
        <?php endif; ?>
    </header>

    <?php if ($poem['categories'] !== []): ?>
    <div class="mb-8 flex flex-wrap gap-2">
        <?php foreach ($poem['categories'] as $category): ?>
        <a href="/categories/view.php?id=<?= $category['id'] ?>" class="rounded-full border border-stone-200 px-3 py-1 text-sm text-stone-600 hover:border-amber-300 hover:text-amber-700 transition-colors"><?= e($category['name']) ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($poem['description'] !== ''): ?>
    <div class="mb-10 border-y border-stone-100 py-6">
        <h2 class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600">Description</h2>
        <div class="poem-content prose prose-stone max-w-none text-stone-600"><?= sanitize_html($poem['description']) ?></div>
    </div>
    <?php endif; ?>

    <div class="poem-content prose prose-stone max-w-none"><?= sanitize_html($poem['content']) ?></div>

    <section class="mt-10 border-t border-stone-100 pt-6">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-600">Submissions</h2>
            <a href="/submissions/create.php" class="text-sm font-semibold text-amber-700 hover:text-amber-800">+ Record submission</a>
        </div>
        <?php if ($poem['submissions'] === []): ?>
        <p class="text-sm text-stone-500">No submissions recorded for this poem.</p>
        <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($poem['submissions'] as $submission): ?>
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-stone-200 bg-white px-4 py-3 text-sm">
                <div><p class="font-medium text-stone-800"><?= e($submission['call_name']) ?> · <?= e($submission['publisher_name']) ?></p><p class="text-xs text-stone-500"><?= e($submission['contact_name'] ?? 'No specific contact') ?> · Submitted <?= date('M j, Y', strtotime($submission['submitted_date'])) ?></p></div>
                <span class="text-stone-500">Closes <?= date('M j, Y', strtotime($submission['closing_date'])) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <?php if ($poem['versions'] !== []): ?>
    <section class="mt-10 border-t border-stone-100 pt-6">
        <h2 class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600">Published versions</h2>
        <div class="space-y-2">
            <?php foreach ($poem['versions'] as $version): ?>
            <a href="/poems/view.php?id=<?= $version['id'] ?>" class="flex items-center justify-between rounded-md border border-stone-200 px-4 py-3 text-sm hover:border-amber-300">
                <span class="font-medium text-stone-700">Version <?= $version['version_number'] ?> · <?= e($version['title']) ?></span>
                <span class="text-stone-500"><?= e($version['written_date']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <footer class="mt-10 flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
        <a href="/poems/edit.php?id=<?= $poem['id'] ?>" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Edit</a>
        <form method="post" action="/poems/delete.php" onsubmit="return confirm('Delete this poem? This cannot be undone.')">
            <input type="hidden" name="id" value="<?= $poem['id'] ?>">
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Delete</button>
        </form>
    </footer>
</article>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
