<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/">Dashboard</a>
    <span>/</span>
    <span aria-current="page">Poems</span>
</nav>

<header class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-stone-200 pb-6">
    <div>
        <h1 class="font-display text-4xl text-stone-900">Poems</h1>
    </div>
    <a href="/poems/create.php" class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors">+ New poem</a>
</header>

<?php if ($poems === []): ?>
<div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-stone-200 bg-white py-20 text-center">
    <p class="mb-1 text-lg font-display text-stone-500">No poems yet.</p>
    <p class="mb-6 text-sm text-stone-400">Start building your library.</p>
    <a href="/poems/create.php" class="rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 transition-colors">Create a poem</a>
</div>
<?php else: ?>
<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm">
    <table class="w-full min-w-[40rem] text-left text-sm">
        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-widest text-stone-600">
            <tr>
                <th scope="col" class="px-6 py-4 font-semibold">Title</th>
                <th scope="col" class="px-6 py-4 font-semibold">Version</th>
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
                <th scope="row" class="max-w-[15rem] px-6 py-5 font-display text-lg font-normal text-stone-900 <?= $poem['depth'] > 0 ? 'border-l-2 border-amber-200 pl-10' : '' ?>">
                    <a href="/poems/view.php?id=<?= $poem['id'] ?>" class="block truncate hover:text-amber-700 transition-colors"><?= e($poem['title']) ?></a>
                </th>
                <td class="whitespace-nowrap px-6 py-5 text-xs font-semibold uppercase tracking-wider text-amber-700">Version <?= $poem['version_number'] ?></td>
                <td class="max-w-[24rem] px-6 py-5 text-stone-500">
                    <span class="block truncate"><?= e($firstLine) ?></span>
                </td>
                <td class="whitespace-nowrap px-6 py-5 text-stone-500">
                    <time datetime="<?= e($poem['written_date']) ?>"><?= date('M j, Y', strtotime($poem['written_date'])) ?></time>
                </td>
                <td class="whitespace-nowrap px-6 py-5 text-right text-xs">
                    <div class="inline-flex items-center gap-1">
                        <a href="/poems/view.php?id=<?= $poem['id'] ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-[#303030] hover:border-stone-300 hover:text-[#303030] transition-colors" aria-label="View poem">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M10 3.5c4.2 0 7.5 4.1 7.5 6.5S14.2 16.5 10 16.5 2.5 12.4 2.5 10 5.8 3.5 10 3.5Zm0 1.5A7.8 7.8 0 0 0 4.1 10 7.8 7.8 0 0 0 10 15a7.8 7.8 0 0 0 5.9-5A7.8 7.8 0 0 0 10 5Zm0 2.3A2.2 2.2 0 1 1 10 12a2.2 2.2 0 0 1 0-4.2Z"/></svg>
                        </a>
                        <a href="/poems/edit.php?id=<?= $poem['id'] ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-[#303030] hover:border-stone-300 hover:text-[#303030] transition-colors" aria-label="Edit poem">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M12.8 3.3a1.7 1.7 0 0 1 2.4 0l1.5 1.5a1.7 1.7 0 0 1 0 2.4L8.2 13.2 5 14l.8-3.2 7-7.5Zm-1.5 1.5L5.3 12.3l-.8 3.2 3.2-.8L13.1 7l-1.8-1.8ZM15.7 5l-.5-.5a.7.7 0 0 0-1 0l-.6.6.5.5.6-.6a.7.7 0 0 1 1 0Z"/></svg>
                        </a>
                        <form method="post" action="/poems/delete.php" class="inline" onsubmit="return confirm('Delete this poem? This cannot be undone.')">
                            <input type="hidden" name="id" value="<?= $poem['id'] ?>">
                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-red-600 hover:border-red-200 hover:text-red-700 transition-colors" aria-label="Delete poem">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M6 4.5A1.5 1.5 0 0 1 7.5 3h5A1.5 1.5 0 0 1 14 4.5V5h1.5a.8.8 0 0 1 0 1.6H15v8.9A2.5 2.5 0 0 1 12.5 18h-5A2.5 2.5 0 0 1 5 15.5V6.6H3.5a.8.8 0 0 1 0-1.6H5v-.5Zm2 0V5h4v-.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5V4.5Zm-1 2.1v8.9a.9.9 0 0 0 .9.9h5.2a.9.9 0 0 0 .9-.9V6.6h-7.1Zm1.9 1.9h1.1v6H9.1v-6Zm3.1 0h1.1v6h-1.1v-6Z"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
