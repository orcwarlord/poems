<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/">Dashboard</a>
    <span>/</span>
    <a href="/categories/">Categories</a>
    <span>/</span>
    <span aria-current="page"><?= e($category['name']) ?></span>
</nav>

<article class="mx-auto max-w-3xl">
    <header class="mb-8 border-b border-stone-200 pb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Category</p>
        <h1 class="mt-3 font-display text-4xl leading-tight text-stone-900 sm:text-5xl"><?= e($category['name']) ?></h1>
        <p class="mt-2 text-sm text-stone-500">Created <?= date('F j, Y', strtotime($category['created_at'])) ?></p>
    </header>

    <footer class="mt-10 flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
        <a href="/categories/edit.php?id=<?= $category['id'] ?>" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Edit</a>
        <form method="post" action="/categories/delete.php" onsubmit="return confirm('Delete this category? This cannot be undone.')">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Delete</button>
        </form>
    </footer>
</article>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>