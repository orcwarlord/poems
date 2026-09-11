<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/">Dashboard</a>
    <span>/</span>
    <a href="/categories/">Categories</a>
    <span>/</span>
    <span aria-current="page"><?= e($heading) ?></span>
</nav>

<div class="mx-auto max-w-3xl">
    <h1 class="mb-8 font-display text-4xl text-stone-900"><?= e($heading) ?></h1>

    <form method="post" class="space-y-6" novalidate>
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-stone-700">Name <span class="text-red-500" aria-hidden="true">*</span></label>
            <input type="text" id="name" name="name" required value="<?= e($category['name']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors" placeholder="e.g. Nature">
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
            <a href="<?= e($cancelUrl) ?>" class="rounded-md border border-stone-300 bg-white px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Cancel</a>
            <button type="submit" class="rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"><?= e($submitLabel) ?></button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>