<?php
/**
 * Shared page footer.
 *
 * Expected variables (set by calling page):
 *   bool   $useEditor     — load and initialise Quill
 *   string $editorContent — (optional) HTML to pre-load into the poem editor
 *   string $descriptionEditorContent — (optional) HTML to pre-load into the description editor
 */
?>
</main><!-- /main -->

<?php
$footerSummaryTotal = $footerSummaryTotal ?? null;
$footerSummaryLatest = $footerSummaryLatest ?? null;
?>

<footer class="mt-auto border-t border-stone-200 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/70">
    <div class="mx-auto flex max-w-[1920px] items-center justify-end gap-6 px-6 py-4 text-sm text-stone-500 lg:px-10">
        <?php if ($footerSummaryTotal !== null): ?>
            <div class="flex items-baseline gap-2">
                <span class="text-[10px] font-medium uppercase tracking-[0.18em] text-stone-400">Poems</span>
                <span class="font-display text-xl font-medium text-stone-500"><?= e((string) $footerSummaryTotal) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($footerSummaryLatest !== null): ?>
            <div class="flex items-baseline gap-2">
                <span class="text-[10px] font-medium uppercase tracking-[0.18em] text-stone-400">Latest</span>
                <span class="font-medium text-stone-500"><?= e((string) $footerSummaryLatest) ?></span>
            </div>
        <?php endif; ?>
    </div>
</footer>

<?php if (!empty($useEditor)): ?>
<!-- ── Quill rich-text editor ──────────────────────────────────────────────── -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    'use strict';

    const editorOptions = {
        theme: 'snow',
        placeholder: 'Begin writing your poem…',
        modules: {
            toolbar: [
                [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ align: [] }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ indent: '-1' }, { indent: '+1' }],
                [{ script: 'sub' }, { script: 'super' }],
                ['blockquote', 'code-block'],
                ['link', 'clean'],
            ],
        },
    };

    const editors = [
        { selector: '#editor', input: '#content', content: <?= json_encode($editorContent ?? '') ?>, placeholder: 'Begin writing your poem…' },
        { selector: '#description-editor', input: '#description', content: <?= json_encode($descriptionEditorContent ?? '') ?>, placeholder: 'Add supporting text…' },
    ].filter(({ selector }) => document.querySelector(selector));

    editors.forEach(({ selector, input, content, placeholder }) => {
        const quill = new Quill(selector, { ...editorOptions, placeholder });
        if (content) {
            quill.root.innerHTML = content;
        }

        const form = document.getElementById('poem-form');
        if (form) {
            form.addEventListener('submit', function () {
                document.querySelector(input).value = quill.root.innerHTML;
            });
        }
    });
}());
</script>
<?php endif; ?>

</body>
</html>
