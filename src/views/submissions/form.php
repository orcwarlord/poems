<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/">Dashboard</a>
    <span>/</span>
    <a href="/submissions/">Submissions</a>
    <span>/</span>
    <span aria-current="page"><?= e($heading) ?></span>
</nav>
<div class="mx-auto max-w-3xl">
    <h1 class="mb-8 font-display text-4xl text-stone-900"><?= e($heading) ?></h1>

    <?php if ($errors !== []): ?>
    <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <ul class="list-disc space-y-1 pl-5"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endif; ?>

    <form method="post" class="space-y-6" novalidate>
        <div>
            <label for="call_name" class="mb-1.5 block text-sm font-medium text-stone-700">Call / Competition <span class="text-red-500">*</span></label>
            <input id="call_name" name="call_name" required value="<?= e($submission['call_name']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5">
        </div>

        <div>
            <label for="poem_id" class="mb-1.5 block text-sm font-medium text-stone-700">Poem <span class="text-red-500">*</span></label>
            <select id="poem_id" name="poem_id" required class="w-full rounded-md border border-stone-300 bg-white px-3 py-2.5">
                <option value="0">Choose a poem</option>
                <?php foreach ($poems as $poem): ?>
                <option value="<?= $poem['id'] ?>" <?= (int) ($submission['poem_id'] ?? 0) === (int) $poem['id'] ? 'selected' : '' ?>><?= e($poem['title']) ?> (<?= e($poem['written_date']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between gap-3">
                <label for="publisher_id" class="block text-sm font-medium text-stone-700">Publisher <span class="text-red-500">*</span></label>
                <button type="button" id="open-publisher-modal" class="text-sm font-semibold text-amber-700 hover:text-amber-800">+ Add publisher</button>
            </div>
            <select id="publisher_id" name="publisher_id" class="w-full rounded-md border border-stone-300 bg-white px-3 py-2.5">
                <option value="0">Choose a publisher</option>
                <?php foreach ($publishers as $publisher): ?>
                <option
                    value="<?= $publisher['id'] ?>"
                    data-name="<?= e($publisher['name']) ?>"
                    data-contact="<?= e($publisher['contact'] ?? '') ?>"
                    data-email="<?= e($publisher['email'] ?? '') ?>"
                    data-url="<?= e($publisher['url'] ?? '') ?>"
                    data-telephone="<?= e($publisher['telephone'] ?? '') ?>"
                    <?= (int) ($submission['publisher_id'] ?? 0) === (int) $publisher['id'] ? 'selected' : '' ?>
                ><?= e($publisher['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="mt-2 text-xs text-stone-400">Choose an existing publisher or add a one-off publisher for this submission.</p>
            <input type="hidden" id="publisher_name" name="publisher_name" value="<?= e($submission['publisher_name'] ?? '') ?>">
            <input type="hidden" id="publisher_contact_name" name="publisher_contact_name" value="<?= e($submission['publisher_contact_name'] ?? '') ?>">
            <input type="hidden" id="publisher_email" name="publisher_email" value="<?= e($submission['publisher_email'] ?? '') ?>">
            <input type="hidden" id="publisher_url" name="publisher_url" value="<?= e($submission['publisher_url'] ?? '') ?>">
            <input type="hidden" id="publisher_telephone" name="publisher_telephone" value="<?= e($submission['publisher_telephone'] ?? '') ?>">

            <div id="publisher-details" class="mt-3 hidden rounded-md border border-stone-200 bg-stone-50 p-3 text-sm text-stone-600">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <p class="text-[11px] uppercase tracking-[0.15em] text-stone-400">Publisher</p>
                        <p id="publisher-detail-name" class="mt-1 font-medium text-stone-700">—</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.15em] text-stone-400">Contact</p>
                        <p id="publisher-detail-contact" class="mt-1 font-medium text-stone-700">—</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.15em] text-stone-400">Email</p>
                        <p id="publisher-detail-email" class="mt-1 font-medium text-stone-700">—</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-[11px] uppercase tracking-[0.15em] text-stone-400">URL</p>
                        <p id="publisher-detail-url" class="mt-1 font-medium text-stone-700">—</p>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label for="publisher_contact_id" class="mb-1.5 block text-sm font-medium text-stone-700">Submission Contact</label>
            <select id="publisher_contact_id" name="publisher_contact_id" class="w-full rounded-md border border-stone-300 bg-white px-3 py-2.5">
                <option value="0">No specific contact</option>
                <?php foreach ($publishers as $publisher): ?>
                    <?php foreach ($publisher['contacts'] ?? [] as $contact): ?>
                    <option data-publisher="<?= $publisher['id'] ?>" value="<?= $contact['id'] ?>" <?= (int) ($submission['publisher_contact_id'] ?? 0) === (int) $contact['id'] ? 'selected' : '' ?>><?= e($publisher['name'] . ' · ' . $contact['name']) ?></option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
            <p class="mt-2 text-xs text-stone-400">Optional if different from the publisher contact already entered.</p>
        </div>

        <div>
            <label for="submission_url" class="mb-1.5 block text-sm font-medium text-stone-700">Submission URL</label>
            <input type="text" id="submission_url" name="submission_url" value="<?= e($submission['submission_url']) ?>" placeholder="example.com/submit or https://example.com/submit" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5">
            <p class="mt-2 text-xs text-stone-400">Enter a URL or domain; HTTPS is optional.</p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="closing_date" class="mb-1.5 block text-sm font-medium text-stone-700">Closing Date <span class="text-red-500">*</span></label>
                <input type="date" id="closing_date" name="closing_date" required value="<?= e($submission['closing_date']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5">
            </div>
            <div>
                <label for="submitted_date" class="mb-1.5 block text-sm font-medium text-stone-700">Date of Submission <span class="text-red-500">*</span></label>
                <input type="date" id="submitted_date" name="submitted_date" required value="<?= e($submission['submitted_date']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5">
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-stone-100 pt-5">
            <a href="<?= e($cancelUrl) ?>" class="rounded-md border border-stone-300 bg-white px-5 py-2.5 text-sm font-medium">Cancel</a>
            <button type="submit" class="rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white"><?= e($submitLabel) ?></button>
        </div>
    </form>
</div>

<div id="publisher-modal" class="fixed inset-0 z-30 hidden items-center justify-center bg-stone-900/40 px-6" role="dialog" aria-modal="true" aria-labelledby="publisher-modal-title">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="publisher-modal-title" class="font-display text-2xl text-stone-900">New publisher</h2>
                <p class="mt-1 text-sm text-stone-500">Create it for this submission or keep it transient.</p>
            </div>
            <button type="button" id="close-publisher-modal" class="text-2xl leading-none text-stone-400 hover:text-stone-700" aria-label="Close">&times;</button>
        </div>

        <form id="publisher-modal-form" class="mt-6 space-y-4">
            <div>
                <label for="new-publisher-name" class="mb-1.5 block text-sm font-medium text-stone-700">Publisher Name</label>
                <input type="text" id="new-publisher-name" name="name" class="w-full rounded-md border border-stone-300 px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="e.g. Riverlight Review">
                <p id="publisher-modal-error" class="mt-2 hidden text-sm text-red-600" role="alert"></p>
            </div>

            <div>
                <label for="new-publisher-contact" class="mb-1.5 block text-sm font-medium text-stone-700">Contact Name (optional)</label>
                <input type="text" id="new-publisher-contact" name="contact" class="w-full rounded-md border border-stone-300 px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="e.g. Sam Jones">
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="new-publisher-email" class="mb-1.5 block text-sm font-medium text-stone-700">Email</label>
                    <input type="email" id="new-publisher-email" name="email" class="w-full rounded-md border border-stone-300 px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="optional">
                </div>
                <div>
                    <label for="new-publisher-url" class="mb-1.5 block text-sm font-medium text-stone-700">URL</label>
                    <input type="text" id="new-publisher-url" name="url" class="w-full rounded-md border border-stone-300 px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="example.com or https://example.com">
                    <p class="mt-2 text-xs text-stone-400">Enter a URL or domain; HTTPS is optional.</p>
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-md border border-stone-200 bg-white px-3 py-2 text-sm text-stone-700">
                <input type="checkbox" id="save-as-publisher" checked class="h-4 w-4 accent-amber-500">
                Save this publisher for future submissions
            </label>

            <div class="flex justify-end gap-3 border-t border-stone-100 pt-4">
                <button type="button" id="cancel-publisher-modal" class="rounded-md border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Cancel</button>
                <button type="submit" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">Use publisher</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const publisherSelect = document.getElementById('publisher_id');
    const publisherNameInput = document.getElementById('publisher_name');
    const publisherContactNameInput = document.getElementById('publisher_contact_name');
    const publisherEmailInput = document.getElementById('publisher_email');
    const publisherUrlInput = document.getElementById('publisher_url');
    const publisherTelephoneInput = document.getElementById('publisher_telephone');
    const publisherDetails = document.getElementById('publisher-details');
    const publisherDetailName = document.getElementById('publisher-detail-name');
    const publisherDetailContact = document.getElementById('publisher-detail-contact');
    const publisherDetailEmail = document.getElementById('publisher-detail-email');
    const publisherDetailUrl = document.getElementById('publisher-detail-url');
    const contacts = document.getElementById('publisher_contact_id');
    const modal = document.getElementById('publisher-modal');
    const open = document.getElementById('open-publisher-modal');
    const close = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
    const error = document.getElementById('publisher-modal-error');

    function syncPublisherDetails(name, contact, email, url, telephone) {
        publisherNameInput.value = name || '';
        publisherContactNameInput.value = contact || '';
        publisherEmailInput.value = email || '';
        publisherUrlInput.value = url || '';
        publisherTelephoneInput.value = telephone || '';

        const hasDetails = [name, contact, email, url, telephone].some((value) => (value || '').trim() !== '');
        publisherDetails.classList.toggle('hidden', !hasDetails);
        publisherDetailName.textContent = name || '—';
        publisherDetailContact.textContent = contact || '—';
        publisherDetailEmail.textContent = email || '—';
        publisherDetailUrl.textContent = url || '—';
    }

    function syncPublisherMode() {
        const hasSavedPublisher = publisherSelect.value !== '0';
        if (hasSavedPublisher) {
            const selectedOption = publisherSelect.selectedOptions[0];
            if (selectedOption) {
                syncPublisherDetails(
                    selectedOption.dataset.name || '',
                    selectedOption.dataset.contact || '',
                    selectedOption.dataset.email || '',
                    selectedOption.dataset.url || '',
                    selectedOption.dataset.telephone || ''
                );
            }
            return;
        }

        const transientName = publisherNameInput.value.trim();
        const transientContact = publisherContactNameInput.value.trim();
        const transientEmail = publisherEmailInput.value.trim();
        const transientUrl = publisherUrlInput.value.trim();
        const transientTelephone = publisherTelephoneInput.value.trim();

        if (transientName || transientContact || transientEmail || transientUrl || transientTelephone) {
            syncPublisherDetails(transientName, transientContact, transientEmail, transientUrl, transientTelephone);
            return;
        }

        publisherDetails.classList.add('hidden');
    }

    function filterContacts() {
        const selected = publisherSelect.value;
        Array.from(contacts.options).forEach((option) => {
            option.hidden = option.value !== '0' && option.dataset.publisher !== selected;
        });
        if (contacts.selectedOptions[0] && contacts.selectedOptions[0].hidden) {
            contacts.value = '0';
        }
    }

    publisherSelect.addEventListener('change', () => {
        filterContacts();
        syncPublisherMode();
    });
    filterContacts();
    syncPublisherMode();

    open.addEventListener('click', () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('new-publisher-name').focus();
    });
    document.getElementById('close-publisher-modal').addEventListener('click', close);
    document.getElementById('cancel-publisher-modal').addEventListener('click', close);
    modal.addEventListener('click', (event) => { if (event.target === modal) close(); });

    document.getElementById('publisher-modal-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        error.classList.add('hidden');

        const name = document.getElementById('new-publisher-name').value.trim();
        if (name === '') {
            error.textContent = 'A publisher name is required.';
            error.classList.remove('hidden');
            return;
        }

        const saveAsPublisher = document.getElementById('save-as-publisher').checked;
        const contactName = document.getElementById('new-publisher-contact').value.trim();
        const formData = new FormData(event.target);
        formData.append('name', name);
        formData.append('contact', contactName);

        if (contactName !== '') {
            formData.append('contacts[0][name]', contactName);
        }

        if (saveAsPublisher) {
            const response = await fetch('/publishers/create-inline.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (!response.ok) {
                error.textContent = result.error || 'The publisher could not be created.';
                error.classList.remove('hidden');
                return;
            }

            const newOption = new Option(result.name, result.id, false, true);
            publisherSelect.add(newOption);
            publisherSelect.value = String(result.id);
            syncPublisherDetails(result.name, result.contact || contactName, result.email || '', result.url || '', result.telephone || '');
            contacts.value = '0';
        } else {
            publisherSelect.value = '0';
            syncPublisherDetails(name, contactName, document.getElementById('new-publisher-email').value.trim(), document.getElementById('new-publisher-url').value.trim(), '');
        }

        close();
        event.target.reset();
    });
}());
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
