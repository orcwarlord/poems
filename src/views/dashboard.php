<?php require_once __DIR__ . '/../includes/header.php'; ?>

<header class="mb-10 border-b border-stone-200 pb-8">
    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-amber-600">A quiet archive</p>
    <h1 class="font-display text-5xl tracking-tight text-stone-900 sm:text-6xl">Verse Library</h1>
    <p class="mt-3 max-w-md text-stone-500">Keep the poems that matter close, beautifully arranged and easy to find.</p>
</header>

<div class="mb-10 grid grid-cols-2 divide-x divide-stone-100 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="px-6 py-5 text-center">
        <p class="font-display text-3xl font-semibold text-stone-900"><?= $total ?></p>
        <p class="mt-1 text-xs uppercase tracking-widest text-stone-400">Poems</p>
    </div>
    <div class="px-6 py-5 text-center">
        <p class="font-display text-3xl font-semibold text-stone-900"><?= $latestDate ? date('M j', strtotime($latestDate)) : '—' ?></p>
        <p class="mt-1 text-xs uppercase tracking-widest text-stone-400">Latest</p>
    </div>
</div>

<section class="mb-10">
    <div class="mb-5 flex items-baseline justify-between">
        <div>
            <h2 class="font-display text-2xl text-stone-700">Categories</h2>
            <p class="mt-1 text-sm text-stone-400"><?= $totalCategories ?> <?= $totalCategories === 1 ? 'category' : 'categories' ?> in your library</p>
        </div>
        <a href="/categories/" class="text-sm font-semibold text-amber-700 hover:text-amber-800 transition-colors">Manage categories</a>
    </div>
    <?php if ($categories === []): ?>
    <div class="rounded-xl border border-dashed border-stone-200 bg-white px-6 py-8 text-center text-sm text-stone-400">No categories yet. <a href="/categories/create.php" class="font-semibold text-amber-700 hover:text-amber-800">Create one</a></div>
    <?php else: ?>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($categories as $category): ?>
        <a href="/categories/view.php?id=<?= $category['id'] ?>" class="rounded-full border border-stone-200 bg-white px-4 py-2 text-sm text-stone-600 shadow-sm hover:border-amber-300 hover:text-amber-700 transition-colors"><?= e($category['name']) ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php if ($poems === []): ?>
<div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-stone-200 bg-white py-24 text-center">
    <p class="mb-1 text-lg font-display text-stone-500">Your first poem belongs here.</p>
    <p class="mb-6 text-sm text-stone-400">Start building your collection.</p>
    <a href="/poems/create.php" class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 transition-colors">Write your first poem</a>
</div>
<?php else: ?>
<div class="mb-5 flex items-baseline justify-between">
    <div>
        <h2 class="font-display text-2xl text-stone-700">Your poems</h2>
        <p class="mt-1 text-sm text-stone-400"><?= $total ?> <?= $total === 1 ? 'poem' : 'poems' ?> in your library</p>
    </div>
    <a href="/poems/create.php" class="inline-flex items-center gap-1.5 rounded-md border border-amber-500 px-3 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 transition-colors">+ New poem</a>
</div>

<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm">
    <table class="w-full min-w-[40rem] text-left text-sm">
        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-widest text-stone-400">
            <tr>
                <th scope="col" class="px-6 py-4 font-semibold">Title</th>
                <th scope="col" class="px-6 py-4 font-semibold">First line</th>
                <th scope="col" class="px-6 py-4 font-semibold">Written</th>
                <th scope="col" class="px-6 py-4 text-right font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            <?php foreach ($poems as $poem): ?>
            <?php
                $withLineBreaks = preg_replace(
                    ['/<\s*br\s*\/?>/i', '/<\/(p|div|h[1-6]|li|blockquote|pre)>/i'],
                    "\n",
                    $poem['content']
                );
                $lines = preg_split('/\R+/', trim(strip_tags($withLineBreaks)), -1, PREG_SPLIT_NO_EMPTY);
                $firstLine = trim($lines[0] ?? '');
            ?>
            <tr class="group hover:bg-amber-50/40">
                <th scope="row" class="max-w-[15rem] px-6 py-5 font-display text-lg font-normal text-stone-900">
                    <a href="/poems/view.php?id=<?= $poem['id'] ?>" class="block truncate hover:text-amber-700 transition-colors"><?= e($poem['title']) ?></a>
                </th>
                <td class="max-w-[24rem] px-6 py-5 text-stone-500">
                    <span class="block truncate"><?= e($firstLine) ?></span>
                </td>
                <td class="whitespace-nowrap px-6 py-5 text-stone-400">
                    <time datetime="<?= e($poem['written_date']) ?>"><?= date('M j, Y', strtotime($poem['written_date'])) ?></time>
                </td>
                <td class="whitespace-nowrap px-6 py-5 text-right text-xs">
                    <a href="/poems/view.php?id=<?= $poem['id'] ?>" class="px-2 py-1 text-stone-500 hover:text-stone-800 transition-colors">View</a>
                    <a href="/poems/edit.php?id=<?= $poem['id'] ?>" class="px-2 py-1 text-stone-500 hover:text-stone-800 transition-colors">Edit</a>
                    <form method="post" action="/poems/delete.php" class="inline" onsubmit="return confirm('Delete this poem? This cannot be undone.')">
                        <input type="hidden" name="id" value="<?= $poem['id'] ?>">
                        <button type="submit" class="px-2 py-1 text-stone-400 hover:text-red-600 transition-colors">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
