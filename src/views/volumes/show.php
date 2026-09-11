<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <div class="flex flex-wrap items-center gap-3 text-sm text-stone-500">
        <a href="/" class="hover:text-amber-700 transition-colors">Dashboard</a>
        <span>/</span>
        <a href="/volumes/" class="hover:text-amber-700 transition-colors">Volumes</a>
    </div>
</nav>

<article class="mx-auto max-w-4xl">
    <header class="mb-8 border-b border-stone-200 pb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Volume</p>
        <h1 class="mt-3 font-display text-4xl leading-tight text-stone-900 sm:text-5xl"><?= e($volume['name']) ?></h1>
        <p class="mt-2 text-sm text-stone-500">Created <?= date('F j, Y', strtotime($volume['created_at'])) ?></p>
    </header>

    <section class="rounded-xl border border-stone-200 bg-white p-6">
        <h2 class="mb-3 font-display text-2xl text-stone-900">Description</h2>
        <p class="whitespace-pre-wrap text-stone-700"><?= e($volume['description']) ?></p>
    </section>

    <section class="mt-8">
        <h2 class="mb-4 font-display text-2xl text-stone-900">Poems in this volume</h2>
        <?php if ($volume['poems'] === []): ?>
        <p class="text-sm text-stone-500">No poems have been added to this volume yet.</p>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($volume['poems'] as $poem): ?>
            <a href="/poems/view.php?id=<?= $poem['id'] ?>" class="block rounded-md border border-stone-200 bg-white px-4 py-3 text-stone-700 transition-colors hover:border-amber-300 hover:bg-amber-50/40">
                <span class="font-medium text-stone-900"><?= e($poem['title']) ?></span>
                <span class="mt-1 block text-xs text-stone-500"><?= e($poem['written_date']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <footer class="mt-10 flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
        <a href="/volumes/edit.php?id=<?= $volume['id'] ?>" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Edit</a>
        <form method="post" action="/volumes/delete.php" onsubmit="return confirm('Delete this volume? This cannot be undone.')">
            <input type="hidden" name="id" value="<?= $volume['id'] ?>">
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Delete</button>
        </form>
    </footer>
</article>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
