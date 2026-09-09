<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/publishers/" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-amber-700 transition-colors">&larr; Back to publishers</a>
</nav>

<article class="mx-auto max-w-3xl">
    <header class="mb-8 border-b border-stone-200 pb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Publisher</p>
        <h1 class="mt-3 font-display text-4xl leading-tight text-stone-900 sm:text-5xl"><?= e($publisher['name']) ?></h1>
        <p class="mt-2 text-sm text-stone-500">Created <?= date('F j, Y', strtotime($publisher['created_at'])) ?></p>
    </header>

    <dl class="grid gap-5 rounded-xl border border-stone-200 bg-white p-6 sm:grid-cols-2">
        <div><dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">Contact</dt><dd class="mt-1 text-stone-800"><?= e($publisher['contact']) ?></dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">Telephone</dt><dd class="mt-1 text-stone-800"><?= e($publisher['telephone']) ?></dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">Email</dt><dd class="mt-1"><a href="mailto:<?= e($publisher['email']) ?>" class="text-amber-700 hover:text-amber-800"><?= e($publisher['email']) ?></a></dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-widest text-stone-600">URL</dt><dd class="mt-1 break-all"><a href="<?= e($publisher['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-amber-700 hover:text-amber-800"><?= e($publisher['url']) ?></a></dd></div>
    </dl>

    <section class="mt-8">
        <h2 class="mb-3 font-display text-2xl text-stone-900">Publisher Contacts</h2>
        <?php if ($publisher['contacts'] === []): ?>
        <p class="text-sm text-stone-500">No additional contacts have been added.</p>
        <?php else: ?>
        <div class="grid gap-3 sm:grid-cols-2">
            <?php foreach ($publisher['contacts'] as $contact): ?>
            <div class="rounded-md border border-stone-200 bg-white p-4">
                <h3 class="font-medium text-stone-800"><?= e($contact['name']) ?></h3>
                <?php if ($contact['email']): ?><a href="mailto:<?= e($contact['email']) ?>" class="mt-1 block text-sm text-amber-700"><?= e($contact['email']) ?></a><?php endif; ?>
                <?php if ($contact['telephone']): ?><p class="mt-1 text-sm text-stone-500"><?= e($contact['telephone']) ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <footer class="mt-10 flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
        <a href="/publishers/edit.php?id=<?= $publisher['id'] ?>" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Edit</a>
        <form method="post" action="/publishers/delete.php" onsubmit="return confirm('Delete this publisher? This cannot be undone.')">
            <input type="hidden" name="id" value="<?= $publisher['id'] ?>">
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Delete</button>
        </form>
    </footer>
</article>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>