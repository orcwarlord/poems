<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/">Dashboard</a>
    <span>/</span>
    <a href="/volumes/">Volumes</a>
    <span>/</span>
    <span aria-current="page"><?= e($heading) ?></span>
</nav>

<div class="mx-auto max-w-4xl">
    <h1 class="mb-8 font-display text-4xl text-stone-900"><?= e($heading) ?></h1>

    <?php if ($errors !== []): ?>
    <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <ul class="list-disc space-y-1 pl-5">
            <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" class="space-y-6" novalidate>
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-stone-700">Volume Name <span class="text-red-500" aria-hidden="true">*</span></label>
            <input type="text" id="name" name="name" required value="<?= e($volume['name']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors" placeholder="e.g. Winter Poems">
        </div>

        <div>
            <label for="description" class="mb-1.5 block text-sm font-medium text-stone-700">Description <span class="text-red-500" aria-hidden="true">*</span></label>
            <textarea id="description" name="description" rows="5" required class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors" placeholder="Write a brief introduction for this volume."><?= e($volume['description']) ?></textarea>
        </div>

        <fieldset>
            <legend class="mb-2 block text-sm font-medium text-stone-700">Poems in this volume</legend>
            <?php if ($poems === []): ?>
            <p class="text-sm text-stone-400">No poems have been created yet.</p>
            <?php else: ?>
            <div class="grid gap-3 md:grid-cols-2">
                <?php foreach ($poems as $poem): ?>
                <?php $selected = in_array((int) $poem['id'], array_map('intval', array_column($volume['poems'], 'id')), true); ?>
                <label class="flex cursor-pointer items-start gap-3 rounded-md border border-stone-200 bg-white px-4 py-3 text-sm text-stone-700 hover:border-amber-300 transition-colors">
                    <input type="checkbox" name="poem_ids[]" value="<?= (int) $poem['id'] ?>" class="mt-1 h-4 w-4 accent-amber-500" <?= $selected ? 'checked' : '' ?>>
                    <span>
                        <span class="block font-medium text-stone-800"><?= e($poem['title']) ?></span>
                        <span class="mt-1 block text-xs text-stone-500"><?= e($poem['written_date']) ?></span>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </fieldset>

        <div class="flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
            <a href="<?= e($cancelUrl) ?>" class="rounded-md border border-stone-300 bg-white px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Cancel</a>
            <button type="submit" class="rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"><?= e($submitLabel) ?></button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
