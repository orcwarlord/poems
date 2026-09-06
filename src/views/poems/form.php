<?php
$editing = $heading === 'Edit Poem';
$useEditor = true;
$editorContent = $poem['content'];
$descriptionEditorContent = $poem['description'];
$selectedCategoryIds = array_map('intval', array_column($poem['categories'], 'id'));
$selectedPhotoId = (int) ($poem['photo_id'] ?? 0);
$photoName = $poem['photo_name'] ?? ($poem['photo_original_name'] ?? '');
$photoTitle = $poem['photo_title'] ?? '';
$photoAltText = $poem['photo_alt_text'] ?? '';
require_once __DIR__ . '/../../includes/header.php';
?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="<?= e($cancelUrl) ?>" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-amber-700 transition-colors">&larr; Back</a>
</nav>

<div class="mx-auto max-w-3xl">
    <h1 class="mb-8 font-display text-4xl text-stone-900"><?= e($heading) ?></h1>

    <form method="post" id="poem-form" class="space-y-6" enctype="multipart/form-data" novalidate>
        <div>
            <label for="title" class="mb-1.5 block text-sm font-medium text-stone-700">Title <span class="text-red-500" aria-hidden="true">*</span></label>
            <input type="text" id="title" name="title" required value="<?= e($poem['title']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors" placeholder="Untitled">
        </div>

        <div>
            <label for="written_date" class="mb-1.5 block text-sm font-medium text-stone-700">Written Date <span class="text-red-500" aria-hidden="true">*</span></label>
            <input type="date" id="written_date" name="written_date" required value="<?= e($poem['written_date']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-colors">
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Description</label>
            <div id="description-editor"></div>
            <textarea id="description" name="description" class="sr-only" aria-hidden="true" tabindex="-1"></textarea>
        </div>

        <fieldset>
            <legend class="mb-2 block text-sm font-medium text-stone-700">Photo</legend>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block rounded-md border border-stone-200 bg-white p-4">
                    <span class="mb-2 block text-sm text-stone-600">Upload a new photo</span>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm text-stone-500 file:mr-3 file:rounded-md file:border-0 file:bg-amber-50 file:px-3 file:py-2 file:font-semibold file:text-amber-700 hover:file:bg-amber-100">
                    <span class="mt-2 block text-xs text-stone-400">The original is kept; an 800px thumbnail is generated.</span>
                    <div class="mt-4 space-y-3">
                        <input type="text" name="photo_name" value="<?= e($photoName) ?>" placeholder="Name (optional; uses filename)" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <input type="text" name="photo_title" value="<?= e($photoTitle) ?>" placeholder="Image title" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <input type="text" name="photo_alt_text" value="<?= e($photoAltText) ?>" placeholder="Alt text" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                </label>
                <label class="block rounded-md border border-stone-200 bg-white p-4">
                    <span class="mb-2 block text-sm text-stone-600">Choose an existing photo</span>
                    <select name="photo_id" class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm text-stone-700 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="0">Use the default image</option>
                        <?php foreach ($photos as $photo): ?>
                        <option value="<?= $photo['id'] ?>" <?= $selectedPhotoId === (int) $photo['id'] ? 'selected' : '' ?>><?= e($photo['title']) ?> · <?= e($photo['original_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
        </fieldset>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Content <span class="text-red-500" aria-hidden="true">*</span></label>
            <div id="editor"></div>
            <textarea id="content" name="content" class="sr-only" aria-hidden="true" tabindex="-1"></textarea>
        </div>

        <fieldset>
            <div class="mb-2 flex items-center justify-between gap-3">
                <legend class="text-sm font-medium text-stone-700">Categories</legend>
                <button type="button" id="open-category-modal" class="text-sm font-semibold text-amber-700 hover:text-amber-800 transition-colors">+ New category</button>
            </div>
            <div id="category-list" class="grid gap-2 sm:grid-cols-2">
                <?php foreach ($categories as $category): ?>
                <label class="flex cursor-pointer items-center gap-3 rounded-md border border-stone-200 bg-white px-4 py-3 text-sm text-stone-700 hover:border-amber-300">
                    <input type="checkbox" name="category_ids[]" value="<?= $category['id'] ?>" class="category-checkbox h-4 w-4 accent-amber-500" <?= in_array((int) $category['id'], $selectedCategoryIds, true) ? 'checked' : '' ?>>
                    <?= e($category['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
            <?php if ($categories === []): ?><p class="mt-2 text-sm text-stone-400">No categories yet.</p><?php endif; ?>
        </fieldset>

        <div class="flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
            <a href="<?= e($cancelUrl) ?>" class="rounded-md border border-stone-300 bg-white px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50 transition-colors">Cancel</a>
            <button type="submit" class="rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"><?= e($submitLabel) ?></button>
        </div>
    </form>
</div>

<div id="category-modal" class="fixed inset-0 z-30 hidden items-center justify-center bg-stone-900/40 px-6" role="dialog" aria-modal="true" aria-labelledby="category-modal-title">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="category-modal-title" class="font-display text-2xl text-stone-900">New category</h2>
                <p class="mt-1 text-sm text-stone-500">Create it and add it to this poem.</p>
            </div>
            <button type="button" id="close-category-modal" class="text-2xl leading-none text-stone-400 hover:text-stone-700" aria-label="Close">&times;</button>
        </div>
        <form id="category-modal-form" class="mt-6 space-y-4">
            <div>
                <label for="new-category-name" class="mb-1.5 block text-sm font-medium text-stone-700">Name</label>
                <input type="text" id="new-category-name" name="name" required class="w-full rounded-md border border-stone-300 px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="e.g. Nature">
                <p id="category-modal-error" class="mt-2 hidden text-sm text-red-600" role="alert"></p>
            </div>
            <div class="flex justify-end gap-3 border-t border-stone-100 pt-4">
                <button type="button" id="cancel-category-modal" class="rounded-md border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Cancel</button>
                <button type="submit" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">Create category</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('category-modal');
    const open = document.getElementById('open-category-modal');
    const close = () => modal.classList.add('hidden');
    const error = document.getElementById('category-modal-error');
    const list = document.getElementById('category-list');

    open.addEventListener('click', () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('new-category-name').focus();
    });
    document.getElementById('close-category-modal').addEventListener('click', close);
    document.getElementById('cancel-category-modal').addEventListener('click', close);
    modal.addEventListener('click', (event) => { if (event.target === modal) close(); });
    document.getElementById('category-modal-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        error.classList.add('hidden');
        const response = await fetch('/categories/create-inline.php', { method: 'POST', body: new FormData(event.target) });
        const result = await response.json();
        if (!response.ok) {
            error.textContent = result.error || 'The category could not be created.';
            error.classList.remove('hidden');
            return;
        }
        const label = document.createElement('label');
        label.className = 'flex cursor-pointer items-center gap-3 rounded-md border border-stone-200 bg-white px-4 py-3 text-sm text-stone-700 hover:border-amber-300';
        label.innerHTML = '<input type="checkbox" name="category_ids[]" value="' + result.id + '" class="category-checkbox h-4 w-4 accent-amber-500" checked> ' + result.name.replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
        list.appendChild(label);
        event.target.reset();
        close();
    });
}());
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
