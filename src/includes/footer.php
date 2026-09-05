<?php
/**
 * Shared page footer.
 *
 * Expected variables (set by calling page):
 *   bool   $useEditor     — load and initialise Quill
 *   string $editorContent — (optional) HTML to pre-load into the editor
 */
?>
</main><!-- /main -->

<?php if (!empty($useEditor)): ?>
<!-- ── Quill rich-text editor ──────────────────────────────────────────────── -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    'use strict';

    const quill = new Quill('#editor', {
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
    });

    // Pre-populate for edit pages
    <?php if (!empty($editorContent)): ?>
    quill.root.innerHTML = <?= json_encode($editorContent) ?>;
    <?php endif; ?>

    // Sync hidden textarea on submit so the value is posted
    const form = document.getElementById('poem-form');
    if (form) {
        form.addEventListener('submit', function () {
            document.getElementById('content').value = quill.root.innerHTML;
        });
    }
}());
</script>
<?php endif; ?>

</body>
</html>
