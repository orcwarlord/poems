<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<header class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-stone-200 pb-6">
    <div>
        <a href="/" class="mb-3 inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-amber-700 transition-colors">&larr; Back to library</a>
        <h1 class="font-display text-4xl text-stone-900">Categories</h1>
    </div>
    <a href="/categories/create.php" class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors">+ New category</a>
</header>

<?php if ($categories === []): ?>
<div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-stone-200 bg-white py-20 text-center">
    <p class="mb-1 text-lg font-display text-stone-500">No categories yet.</p>
    <p class="mb-6 text-sm text-stone-400">Create one to organize your library later.</p>
    <a href="/categories/create.php" class="rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 transition-colors">Create a category</a>
</div>
<?php else: ?>
<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm">
    <table class="w-full min-w-[32rem] text-left text-sm">
        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-widest text-stone-400">
            <tr>
                <th scope="col" class="px-6 py-4 font-semibold">Name</th>
                <th scope="col" class="px-6 py-4 font-semibold">Created</th>
                <th scope="col" class="px-6 py-4 text-right font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            <?php foreach ($categories as $category): ?>
            <tr class="group hover:bg-amber-50/40">
                <th scope="row" class="px-6 py-5 font-display text-lg font-normal text-stone-900"><a href="/categories/view.php?id=<?= $category['id'] ?>" class="hover:text-amber-700 transition-colors"><?= e($category['name']) ?></a></th>
                <td class="whitespace-nowrap px-6 py-5 text-stone-400"><?= date('M j, Y', strtotime($category['created_at'])) ?></td>
                <td class="whitespace-nowrap px-6 py-5 text-right text-xs">
                    <a href="/categories/view.php?id=<?= $category['id'] ?>" class="px-2 py-1 text-stone-500 hover:text-stone-800 transition-colors">View</a>
                    <a href="/categories/edit.php?id=<?= $category['id'] ?>" class="px-2 py-1 text-stone-500 hover:text-stone-800 transition-colors">Edit</a>
                    <form method="post" action="/categories/delete.php" class="inline" onsubmit="return confirm('Delete this category? This cannot be undone.')">
                        <input type="hidden" name="id" value="<?= $category['id'] ?>">
                        <button type="submit" class="px-2 py-1 text-stone-400 hover:text-red-600 transition-colors">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>