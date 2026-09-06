<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="mb-8 flex items-end justify-between gap-4 border-b border-stone-200 pb-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Library</p>
        <h1 class="mt-2 font-display text-4xl text-stone-900">Manage images</h1>
    </div>
    <div class="flex items-center gap-4">
        <a href="/images/create.php" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">New Image</a>
        <a href="/" class="text-sm font-semibold text-amber-700 hover:text-amber-800">Back to library</a>
    </div>
</div>

<?php if ($photos === []): ?>
<div class="rounded-xl border border-dashed border-stone-200 bg-white px-6 py-16 text-center text-sm text-stone-400">No uploaded images yet. Add one while creating a poem.</div>
<?php else: ?>
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($photos as $photo): ?>
    <article class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <img src="<?= e($photo['thumbnail_path']) ?>" alt="<?= e($photo['alt_text']) ?>" class="aspect-[4/3] w-full object-cover">
        <div class="p-5">
            <h2 class="font-display text-xl text-stone-900"><?= e($photo['title']) ?></h2>
            <p class="mt-1 truncate text-sm text-stone-500"><?= e($photo['original_name']) ?></p>
            <p class="mt-2 text-xs text-stone-400">Alt text: <?= e($photo['alt_text']) ?></p>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <a href="/images/edit.php?id=<?= $photo['id'] ?>" class="inline-flex rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">Edit details</a>
                <?php if ($photo['is_default']): ?>
                <span class="rounded-md bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-800">Default</span>
                <?php else: ?>
                <form method="post">
                    <input type="hidden" name="id" value="<?= $photo['id'] ?>">
                    <button type="submit" class="rounded-md px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50">Make default</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>