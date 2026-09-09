<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="<?= e($cancelUrl) ?>" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-amber-700 transition-colors">&larr; Back</a>
</nav>

<div class="mx-auto max-w-3xl">
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
            <label for="name" class="mb-1.5 block text-sm font-medium text-stone-700">Publisher Name <span class="text-red-500" aria-hidden="true">*</span></label>
            <input type="text" id="name" name="name" required value="<?= e($publisher['name']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors">
        </div>
        <div>
            <label for="contact" class="mb-1.5 block text-sm font-medium text-stone-700">Contact</label>
            <input type="text" id="contact" name="contact" value="<?= e($publisher['contact']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors">
        </div>
        <div>
            <label for="url" class="mb-1.5 block text-sm font-medium text-stone-700">URL</label>
            <input type="text" id="url" name="url" value="<?= e($publisher['url']) ?>" placeholder="example.com or https://example.com" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors">
            <p class="mt-2 text-xs text-stone-400">Enter a URL or domain; HTTPS is optional.</p>
        </div>
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="telephone" class="mb-1.5 block text-sm font-medium text-stone-700">Telephone</label>
                <input type="tel" id="telephone" name="telephone" value="<?= e($publisher['telephone']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-stone-700">Email</label>
                <input type="email" id="email" name="email" value="<?= e($publisher['email']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>
        </div>
        <fieldset>
            <div class="mb-2 flex items-center justify-between gap-3">
                <legend class="text-sm font-medium text-stone-700">Publisher Contacts</legend>
                <button type="button" id="add-contact" class="text-sm font-semibold text-amber-700 hover:text-amber-800">+ Add contact</button>
            </div>
            <div id="contacts" class="space-y-3">
                <?php $contacts = $publisher['contacts'] !== [] ? $publisher['contacts'] : [['name' => '', 'email' => '', 'telephone' => '']]; ?>
                <?php foreach ($contacts as $index => $contact): ?>
                <div class="contact-row grid gap-3 rounded-md border border-stone-200 bg-white p-4 sm:grid-cols-[1fr_1fr_1fr_auto]">
                    <input type="text" name="contacts[<?= $index ?>][name]" value="<?= e($contact['name']) ?>" placeholder="Name" class="rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <input type="email" name="contacts[<?= $index ?>][email]" value="<?= e($contact['email']) ?>" placeholder="Email" class="rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <input type="tel" name="contacts[<?= $index ?>][telephone]" value="<?= e($contact['telephone']) ?>" placeholder="Telephone" class="rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <button type="button" class="remove-contact px-2 text-sm text-stone-400 hover:text-red-600">Remove</button>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="mt-2 text-xs text-stone-400">Contacts are optional. Add as many as the publisher provides.</p>
        </fieldset>
        <div class="flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
            <a href="<?= e($cancelUrl) ?>" class="rounded-md border border-stone-300 bg-white px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Cancel</a>
            <button type="submit" class="rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"><?= e($submitLabel) ?></button>
        </div>
    </form>
</div>

<script>
(function () {
    const list = document.getElementById('contacts');
    let nextIndex = <?= count($contacts) ?>;
    document.getElementById('add-contact').addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'contact-row grid gap-3 rounded-md border border-stone-200 bg-white p-4 sm:grid-cols-[1fr_1fr_1fr_auto]';
        row.innerHTML = '<input type="text" name="contacts[' + nextIndex + '][name]" placeholder="Name" class="rounded-md border border-stone-300 px-3 py-2 text-sm">' +
            '<input type="email" name="contacts[' + nextIndex + '][email]" placeholder="Email" class="rounded-md border border-stone-300 px-3 py-2 text-sm">' +
            '<input type="tel" name="contacts[' + nextIndex + '][telephone]" placeholder="Telephone" class="rounded-md border border-stone-300 px-3 py-2 text-sm">' +
            '<button type="button" class="remove-contact px-2 text-sm text-stone-400 hover:text-red-600">Remove</button>';
        list.appendChild(row);
        nextIndex++;
    });
    list.addEventListener('click', (event) => {
        if (event.target.classList.contains('remove-contact')) {
            event.target.closest('.contact-row').remove();
        }
    });
}());
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>