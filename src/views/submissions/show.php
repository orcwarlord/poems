<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/">Dashboard</a>
    <span>/</span>
    <a href="/submissions/">Submissions</a>
    <span>/</span>
    <span aria-current="page"><?= e($submission['call_name']) ?></span>
</nav>

<article class="mx-auto max-w-3xl">
    <header class="mb-8 border-b border-stone-200 pb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Submission</p>
        <h1 class="mt-3 font-display text-4xl leading-tight text-stone-900 sm:text-5xl"><?= e($submission['call_name']) ?></h1>
        <p class="mt-2 text-sm text-stone-500">Submitted <?= date('F j, Y', strtotime($submission['submitted_date'])) ?></p>
    </header>

    <dl class="grid gap-5 rounded-xl border border-stone-200 bg-white p-6 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">Poem</dt>
            <dd class="mt-1 text-stone-800">
                <?php if ($poem !== null): ?>
                    <a href="/poems/view.php?id=<?= (int) $poem['id'] ?>" class="font-medium text-amber-700 hover:text-amber-800"><?= e($poem['title']) ?></a>
                <?php else: ?>
                    <span class="text-stone-500">Poem not available</span>
                <?php endif; ?>
            </dd>
        </div>

        <div>
            <dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">Closing date</dt>
            <dd class="mt-1 text-stone-800"><?= date('F j, Y', strtotime($submission['closing_date'])) ?></dd>
        </div>

        <div>
            <dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">Submitted date</dt>
            <dd class="mt-1 text-stone-800"><?= date('F j, Y', strtotime($submission['submitted_date'])) ?></dd>
        </div>

        <div class="sm:col-span-2">
            <dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">Publisher</dt>
            <dd class="mt-1 text-stone-800">
                <?php if ($publisher !== null): ?>
                    <a href="/publishers/view.php?id=<?= (int) $publisher['id'] ?>" class="font-medium text-amber-700 hover:text-amber-800"><?= e($publisher['name']) ?></a>
                <?php elseif ($submission['publisher_name'] !== ''): ?>
                    <?= e($submission['publisher_name']) ?>
                <?php else: ?>
                    <span class="text-stone-500">No publisher recorded</span>
                <?php endif; ?>
                <?php if (($submission['publisher_contact_id'] ?? null) !== null || ($submission['publisher_contact_name'] ?? '') !== ''): ?>
                    <span class="mt-1 block text-sm text-stone-500"><?= e($submission['publisher_contact_name'] ?: ($publisher['contact'] ?? '')) ?></span>
                <?php endif; ?>
            </dd>
        </div>

        <div class="sm:col-span-2">
            <dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">Submission URL</dt>
            <dd class="mt-1 break-all text-stone-800">
                <?php if ($submission['submission_url'] !== ''): ?>
                    <a href="<?= e($submission['submission_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-amber-700 hover:text-amber-800"><?= e($submission['submission_url']) ?></a>
                <?php else: ?>
                    <span class="text-stone-500">No URL recorded</span>
                <?php endif; ?>
            </dd>
        </div>
    </dl>

    <footer class="mt-10 flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
        <a href="/submissions/edit.php?id=<?= (int) $submission['id'] ?>" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Edit</a>
        <form method="post" action="/submissions/delete.php" onsubmit="return confirm('Delete this submission?')">
            <input type="hidden" name="id" value="<?= (int) $submission['id'] ?>">
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Delete</button>
        </form>
    </footer>
</article>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
