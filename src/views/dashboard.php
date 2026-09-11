<?php require_once __DIR__ . '/../includes/header.php'; ?>

<header class="mb-10 border-b border-stone-200 pb-8">
    <h1 class="font-display text-4xl tracking-tight text-stone-900 sm:text-5xl">Dashboard</h1>
</header>

<section class="mb-10">
    <div class="mb-5 flex items-baseline justify-between">
        <div>
            <h2 class="font-display text-2xl text-stone-700">Submissions</h2>
            <p class="mt-1 text-sm text-stone-500">Upcoming deadlines and recent calls</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/submissions/" class="text-sm font-semibold text-amber-700 hover:text-amber-800 transition-colors">Manage submissions</a>
            <a href="/submissions/create.php" class="inline-flex items-center gap-1.5 rounded-md border border-amber-500 px-3 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 transition-colors">+ New submission</a>
        </div>
    </div>

    <?php if ($submissions === []): ?>
    <div class="rounded-xl border border-dashed border-stone-200 bg-white px-6 py-8 text-center text-sm text-stone-500">No submissions recorded yet.</div>
    <?php else: ?>
    <div class="mb-4 flex flex-wrap items-center gap-3 text-xs text-stone-500">
        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-emerald-50 border border-emerald-200"></span>Upcoming subs</span>
        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-red-50 border border-red-200"></span>Closed</span>
        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-white border border-stone-200"></span>Other</span>
    </div>
    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm">
        <table class="w-full min-w-[60rem] text-left text-sm">
            <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-widest text-stone-600">
                <tr>
                    <th scope="col" class="px-6 py-4 font-semibold">Call / Competition</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Poem</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Publisher</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Closing</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Submitted</th>
                    <th scope="col" class="px-6 py-4 text-right font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <?php foreach ($submissions as $submission): ?>
                <?php
                    $closingDate = new DateTimeImmutable($submission['closing_date']);
                    $today = new DateTimeImmutable('today');
                    $isClosed = $closingDate < $today;
                    $daysUntilClosing = (int) $today->diff($closingDate)->format('%r%a');
                    $isUrgent = !$isClosed && $daysUntilClosing <= 30;
                    $rowClasses = 'group';
                    if ($isClosed) {
                        $rowClasses .= ' bg-red-50/60';
                    } elseif ($isUrgent) {
                        $rowClasses .= ' bg-emerald-50/70';
                    }
                ?>
                <tr class="<?= $rowClasses ?> hover:bg-emerald-50/80">
                    <th scope="row" class="px-6 py-5 font-medium text-stone-900">
                        <?= e($submission['call_name']) ?>
                        <?php if ($isClosed): ?>
                        <span class="ml-2 inline-block rounded-full border border-red-200 bg-red-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.15em] text-red-700">Closed</span>
                        <?php elseif ($isUrgent): ?>
                        <span class="ml-2 inline-block rounded-full border border-emerald-200 bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.15em] text-emerald-700">Soon</span>
                        <?php endif; ?>
                    </th>
                    <td class="px-6 py-5"><a href="/poems/view.php?id=<?= $submission['poem_id'] ?>" class="text-amber-700 hover:text-amber-800"><?= e($submission['poem_title']) ?></a></td>
                    <td class="px-6 py-5 text-stone-500">
                        <?= e($submission['publisher_name'] ?: '—') ?>
                        <?php if ($submission['contact_name']): ?><span class="mt-1 block text-xs text-stone-500"><?= e($submission['contact_name']) ?></span><?php endif; ?>
                    </td>
                    <td class="whitespace-nowrap px-6 py-5 text-stone-500">
                        <time datetime="<?= e($submission['closing_date']) ?>"><?= date('M j, Y', strtotime($submission['closing_date'])) ?></time>
                    </td>
                    <td class="whitespace-nowrap px-6 py-5 text-stone-500">
                        <time datetime="<?= e($submission['submitted_date']) ?>"><?= date('M j, Y', strtotime($submission['submitted_date'])) ?></time>
                    </td>
                    <td class="whitespace-nowrap px-6 py-5 text-right text-xs">
                        <div class="inline-flex items-center gap-1">
                            <a href="/submissions/view.php?id=<?= $submission['id'] ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-[#303030] hover:border-stone-300 hover:text-[#303030] transition-colors" aria-label="View submission">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M10 3.5c4.2 0 7.5 4.1 7.5 6.5S14.2 16.5 10 16.5 2.5 12.4 2.5 10 5.8 3.5 10 3.5Zm0 1.5A7.8 7.8 0 0 0 4.1 10 7.8 7.8 0 0 0 10 15a7.8 7.8 0 0 0 5.9-5A7.8 7.8 0 0 0 10 5Zm0 2.3A2.2 2.2 0 1 1 10 12a2.2 2.2 0 0 1 0-4.2Z"/></svg>
                            </a>
                            <a href="/submissions/edit.php?id=<?= $submission['id'] ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-[#303030] hover:border-stone-300 hover:text-[#303030] transition-colors" aria-label="Edit submission">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M12.8 3.3a1.7 1.7 0 0 1 2.4 0l1.5 1.5a1.7 1.7 0 0 1 0 2.4L8.2 13.2 5 14l.8-3.2 7-7.5Zm-1.5 1.5L5.3 12.3l-.8 3.2 3.2-.8L13.1 7l-1.8-1.8ZM15.7 5l-.5-.5a.7.7 0 0 0-1 0l-.6.6.5.5.6-.6a.7.7 0 0 1 1 0Z"/></svg>
                            </a>
                            <form method="post" action="/submissions/delete.php" class="inline" onsubmit="return confirm('Delete this submission?')">
                                <input type="hidden" name="id" value="<?= $submission['id'] ?>">
                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-stone-200 bg-white text-red-600 hover:border-red-200 hover:text-red-700 transition-colors" aria-label="Delete submission">
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
</section>

<?php if ($poems === []): ?>
<div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-stone-200 bg-white py-24 text-center">
    <p class="mb-1 text-lg font-display text-stone-600">Your first poem belongs here.</p>
    <p class="mb-6 text-sm text-stone-500">Start building your collection.</p>
    <a href="/poems/create.php" class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 transition-colors">Write your first poem</a>
</div>
<?php else: ?>
<div class="mb-5 flex items-baseline justify-between">
    <div>
        <h2 class="font-display text-2xl text-stone-700">Your poems</h2>
        <p class="mt-1 text-sm text-stone-500"><?= $total ?> <?= $total === 1 ? 'poem' : 'poems' ?> in your library</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="/poems/" class="text-sm font-semibold text-amber-700 hover:text-amber-800 transition-colors">Manage poems</a>
        <a href="/poems/create.php" class="inline-flex items-center gap-1.5 rounded-md border border-amber-500 px-3 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 transition-colors">+ New poem</a>
    </div>
</div>

<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm">
    <table class="w-full min-w-[40rem] text-left text-sm">
        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-widest text-stone-400">
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

<section class="mt-10">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-baseline justify-between">
                <div>
                    <h2 class="font-display text-2xl text-stone-700">Categories</h2>
                    <p class="mt-1 text-sm text-stone-500"><?= $totalCategories ?> <?= $totalCategories === 1 ? 'category' : 'categories' ?> in your library</p>
                </div>
                <a href="/categories/" class="text-sm font-semibold text-amber-700 hover:text-amber-800 transition-colors">Manage</a>
            </div>

            <?php if ($categories === []): ?>
            <div class="rounded-xl border border-dashed border-stone-200 bg-stone-50 px-6 py-8 text-center text-sm text-stone-500">No categories yet. <a href="/categories/create.php" class="font-semibold text-amber-700 hover:text-amber-800">Create one</a></div>
            <?php else: ?>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($categories as $category): ?>
                <a href="/categories/view.php?id=<?= $category['id'] ?>" class="rounded-full border border-stone-200 bg-white px-4 py-2 text-sm text-stone-600 shadow-sm hover:border-amber-300 hover:text-amber-700 transition-colors"><?= e($category['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-baseline justify-between">
                <div>
                    <h2 class="font-display text-2xl text-stone-700">Volumes</h2>
                    <p class="mt-1 text-sm text-stone-500"><?= $totalVolumes ?> <?= $totalVolumes === 1 ? 'volume' : 'volumes' ?> in your library</p>
                </div>
                <a href="/volumes/" class="text-sm font-semibold text-amber-700 hover:text-amber-800 transition-colors">Manage</a>
            </div>

            <?php if (empty($volumes)): ?>
            <div class="rounded-xl border border-dashed border-stone-200 bg-stone-50 px-6 py-8 text-center text-sm text-stone-500">No volumes yet. <a href="/volumes/create.php" class="font-semibold text-amber-700 hover:text-amber-800">Create one</a></div>
            <?php else: ?>
            <div class="max-h-56 space-y-2 overflow-y-auto pr-1">
                <?php foreach ($volumes as $volume): ?>
                <a href="/volumes/view.php?id=<?= $volume['id'] ?>" class="block rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-700 transition-colors hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700">
                    <?= e($volume['name']) ?>
                    <span class="mt-1 block text-[11px] text-stone-500"><?= (int) ($volume['poem_count'] ?? 0) ?> <?= ((int) ($volume['poem_count'] ?? 0)) === 1 ? 'poem' : 'poems' ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-baseline justify-between">
                <div>
                    <h2 class="font-display text-2xl text-stone-700">Publishers</h2>
                    <p class="mt-1 text-sm text-stone-500">Keep your publishing contacts together</p>
                </div>
                <a href="/publishers/" class="text-sm font-semibold text-amber-700 hover:text-amber-800 transition-colors">Manage</a>
            </div>

            <?php if (empty($publishers)): ?>
            <div class="rounded-xl border border-dashed border-stone-200 bg-stone-50 px-6 py-8 text-center text-sm text-stone-500">No publishers yet.</div>
            <?php else: ?>
            <div class="max-h-56 space-y-2 overflow-y-auto pr-1">
                <?php foreach ($publishers as $publisher): ?>
                <a href="/publishers/view.php?id=<?= $publisher['id'] ?>" class="block rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-700 transition-colors hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700">
                    <?= e($publisher['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
$footerSummaryTotal = $total;
$footerSummaryLatest = $latestDate ? date('M j', strtotime($latestDate)) : '—';
require_once __DIR__ . '/../includes/footer.php';
?>
