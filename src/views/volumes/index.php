<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/">Dashboard</a>
    <span>/</span>
    <span aria-current="page">Volumes</span>
</nav>

<header class="mb-8 flex items-center justify-between gap-4">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Poetry library</p>
        <h1 class="mt-2 font-display text-4xl text-stone-900">Volumes</h1>
    </div>
    <a href="/volumes/create.php" class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors">+ New volume</a>
</header>

<?php if ($volumes === []): ?>
<div class="rounded-xl border border-dashed border-stone-200 bg-white px-6 py-10 text-center text-sm text-stone-500">No volumes yet. <a href="/volumes/create.php" class="font-semibold text-amber-700 hover:text-amber-800">Create one</a></div>
<?php else: ?>
<div class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
    <?php foreach ($volumes as $volume): ?>
    <article class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm transition-colors hover:border-amber-300 hover:bg-amber-50/30">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Volume</p>
                <h2 class="mt-2 font-display text-2xl text-stone-900"><a href="/volumes/view.php?id=<?= $volume['id'] ?>" class="hover:text-amber-700 transition-colors"><?= e($volume['name']) ?></a></h2>
            </div>
            <span class="rounded-full border border-stone-200 bg-stone-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-stone-600"><?= (int) ($volume['poem_count'] ?? 0) ?> <?= ((int) ($volume['poem_count'] ?? 0)) === 1 ? 'poem' : 'poems' ?></span>
        </div>

        <p class="line-clamp-4 text-sm leading-6 text-stone-600"><?= e($volume['description']) ?></p>

        <div class="mt-5 flex items-center justify-end gap-2 border-t border-stone-100 pt-4">
            <a href="/volumes/view.php?id=<?= $volume['id'] ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-[#303030] hover:border-stone-300 hover:text-[#303030] transition-colors" aria-label="View volume">
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M10 3.5c4.2 0 7.5 4.1 7.5 6.5S14.2 16.5 10 16.5 2.5 12.4 2.5 10 5.8 3.5 10 3.5Zm0 1.5A7.8 7.8 0 0 0 4.1 10 7.8 7.8 0 0 0 10 15a7.8 7.8 0 0 0 5.9-5A7.8 7.8 0 0 0 10 5Zm0 2.3A2.2 2.2 0 1 1 10 12a2.2 2.2 0 0 1 0-4.2Z"/></svg>
            </a>
            <a href="/volumes/edit.php?id=<?= $volume['id'] ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-[#303030] hover:border-stone-300 hover:text-[#303030] transition-colors" aria-label="Edit volume">
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M12.8 3.3a1.7 1.7 0 0 1 2.4 0l1.5 1.5a1.7 1.7 0 0 1 0 2.4L8.2 13.2 5 14l.8-3.2 7-7.5Zm-1.5 1.5L5.3 12.3l-.8 3.2 3.2-.8L13.1 7l-1.8-1.8ZM15.7 5l-.5-.5a.7.7 0 0 0-1 0l-.6.6.5.5.6-.6a.7.7 0 0 1 1 0Z"/></svg>
            </a>
            <form method="post" action="/volumes/delete.php" class="inline" onsubmit="return confirm('Delete this volume? This cannot be undone.')">
                <input type="hidden" name="id" value="<?= $volume['id'] ?>">
                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-red-600 hover:border-red-200 hover:text-red-700 transition-colors" aria-label="Delete volume">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M6 4.5A1.5 1.5 0 0 1 7.5 3h5A1.5 1.5 0 0 1 14 4.5V5h1.5a.8.8 0 0 1 0 1.6H15v8.9A2.5 2.5 0 0 1 12.5 18h-5A2.5 2.5 0 0 1 5 15.5V6.6H3.5a.8.8 0 0 1 0-1.6H5v-.5Zm2 0V5h4v-.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5V4.5Zm-1 2.1v8.9a.9.9 0 0 0 .9.9h5.2a.9.9 0 0 0 .9-.9V6.6h-7.1Zm1.9 1.9h1.1v6H9.1v-6Zm3.1 0h1.1v6h-1.1v-6Z"/></svg>
                </button>
            </form>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
