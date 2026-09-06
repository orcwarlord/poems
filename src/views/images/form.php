<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<?php $isNew = $isNew ?? false; ?>

<nav class="mb-7" aria-label="Breadcrumb">
    <a href="/images/" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-amber-700">&larr; Back to images</a>
</nav>

<div class="mx-auto max-w-2xl">
    <h1 class="mb-8 font-display text-4xl text-stone-900"><?= $isNew ? 'New Image' : 'Edit image details' ?></h1>
    <?php if (!$isNew): ?><div class="mb-8 overflow-hidden rounded-xl border border-stone-200 bg-white">
        <img src="<?= e($photo['thumbnail_path']) ?>" alt="<?= e($photo['alt_text']) ?>" class="max-h-[32rem] w-full object-contain">
    </div><?php endif; ?>
    <form method="post" <?= $isNew ? 'enctype="multipart/form-data"' : '' ?> class="space-y-5">
        <?php if ($isNew): ?>
        <div>
            <label for="photo" class="mb-1.5 block text-sm font-medium text-stone-700">Image</label>
            <input type="file" id="photo" name="photo" required accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm text-stone-500 file:mr-3 file:rounded-md file:border-0 file:bg-amber-50 file:px-3 file:py-2 file:font-semibold file:text-amber-700 hover:file:bg-amber-100">
        </div>
        <?php endif; ?>
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-stone-700">Name <span class="font-normal text-stone-400">(optional)</span></label>
            <input type="text" id="name" name="name" value="<?= e($photo['original_name']) ?>" placeholder="Filename or display name" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <div>
            <label for="title" class="mb-1.5 block text-sm font-medium text-stone-700">Title</label>
            <input type="text" id="title" name="title" required value="<?= e($photo['title']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <div>
            <label for="alt_text" class="mb-1.5 block text-sm font-medium text-stone-700">Alt text</label>
            <input type="text" id="alt_text" name="alt_text" required value="<?= e($photo['alt_text']) ?>" class="w-full rounded-md border border-stone-300 bg-white px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <div class="flex justify-end gap-3 border-t border-stone-100 pt-5">
            <a href="/images/" class="rounded-md border border-stone-300 bg-white px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50">Cancel</a>
            <button type="submit" class="rounded-md bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600"><?= $isNew ? 'Upload image' : 'Save details' ?></button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>