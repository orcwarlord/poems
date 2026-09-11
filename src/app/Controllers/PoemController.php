<?php

declare(strict_types=1);

class PoemController
{
    public function __construct(
        private Poem $poems,
        private Category $categories,
        private Photo $photos,
        private Submission $submissions,
        private Publisher $publishers
    ) {
    }

    public function index(): void
    {
        $poems = $this->poems->all();
        $categories = $this->categories->all();
        $publishers = $this->publishers->all();
        $submissions = $this->submissions->all();
        $volumes = (new Volume(get_db()))->all();

        render('dashboard', [
            'pageTitle' => 'Dashboard',
            'poems' => $poems,
            'total' => count($poems),
            'latestDate' => $poems[0]['created_at'] ?? null,
            'categories' => $categories,
            'totalCategories' => count($categories),
            'publishers' => $publishers,
            'submissions' => $submissions,
            'volumes' => $volumes,
            'totalVolumes' => count($volumes),
        ]);
    }

    public function list(): void
    {
        $poems = $this->poems->all();

        render('poems/index', [
            'pageTitle' => 'Poems',
            'poems' => $poems,
            'total' => count($poems),
            'latestDate' => $poems[0]['created_at'] ?? null,
        ]);
    }

    public function create(): void
    {
        $versionSource = null;
        $versionSourceId = filter_input(INPUT_GET, 'version_of', FILTER_VALIDATE_INT);
        if ($versionSourceId !== false && $versionSourceId !== null && $versionSourceId > 0) {
            $versionSource = $this->findOrFail($versionSourceId);
        }

        $poem = [
            'title' => '',
            'written_date' => date('Y-m-d'),
            'description' => '',
            'content' => '',
            'parent_poem_id' => null,
            'photo_id' => null,
            'photo_name' => '',
            'photo_title' => '',
            'photo_alt_text' => '',
            'categories' => [],
            'volumes' => [],
        ];
        if ($versionSource !== null) {
            $poem['title'] = $versionSource['title'];
            $poem['written_date'] = $versionSource['written_date'];
            $poem['description'] = $versionSource['description'];
            $poem['content'] = $versionSource['content'];
            $poem['parent_poem_id'] = $versionSource['id'];
            $poem['photo_id'] = $versionSource['photo_id'];
            $poem['photo_name'] = $versionSource['photo_original_name'] ?? '';
            $poem['photo_title'] = $versionSource['photo_title'] ?? '';
            $poem['photo_alt_text'] = $versionSource['photo_alt_text'] ?? '';
            $poem['categories'] = $versionSource['categories'];
            $poem['volumes'] = $versionSource['volumes'] ?? [];
        }
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $parentPoemId = (int) ($_POST['parent_poem_id'] ?? 0);
            [$poem, $errors] = $this->formData($parentPoemId > 0 ? $parentPoemId : null);

            if ($errors === []) {
                $this->poems->create($poem['title'], $poem['written_date'], $poem['description'], $poem['content'], $poem['photo_id'], $poem['parent_poem_id'], $poem['categoryIds'], $poem['volumeIds']);
                flash('success', 'Your poem has been saved.');
                header('Location: /');
                exit;
            }
            $versionSource = $poem['parent_poem_id'] === null ? null : $this->poems->find($poem['parent_poem_id']);
        }

        render('poems/form', [
            'pageTitle' => $versionSource === null ? 'New Poem' : 'New Poem Version',
            'heading' => $versionSource === null ? 'New Poem' : 'New Poem Version',
            'submitLabel' => $versionSource === null ? 'Save poem' : 'Save version',
            'cancelUrl' => '/',
            'poem' => $poem,
            'photos' => $this->photos->all(),
            'categories' => $this->categories->all(),
            'volumes' => (new Volume(get_db()))->all(),
            'errors' => $errors,
            'isNew' => true,
            'versionSource' => $versionSource,
            'versionCandidates' => $this->poems->all(),
        ]);
    }

    public function show(?int $id): void
    {
        $poem = $this->findOrFail($id);

        render('poems/show', [
            'pageTitle' => $poem['title'],
            'poem' => $poem,
        ]);
    }

    public function edit(?int $id): void
    {
        $poem = $this->findOrFail($id);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$formData, $errors] = $this->formData(null, $poem['photo_id'] === null ? null : (int) $poem['photo_id']);
            $poem['title'] = $formData['title'];
            $poem['written_date'] = $formData['written_date'];
            $poem['description'] = $formData['description'];
            $poem['content'] = $formData['content'];
            $poem['photo_id'] = $formData['photo_id'];
            $poem['photo_name'] = $formData['photo_name'];
            $poem['photo_title'] = $formData['photo_title'];
            $poem['photo_alt_text'] = $formData['photo_alt_text'];
            $poem['categories'] = $formData['categories'];
            $poem['volumes'] = $formData['volumes'];

            if ($errors === []) {
                $this->poems->update($id, $poem['title'], $poem['written_date'], $poem['description'], $poem['content'], $poem['photo_id'], $formData['categoryIds'], $formData['volumeIds']);
                flash('success', 'Your poem has been updated.');
                header('Location: /poems/view.php?id=' . $id);
                exit;
            }
        }

        render('poems/form', [
            'pageTitle' => 'Edit Poem',
            'heading' => 'Edit Poem',
            'submitLabel' => 'Save changes',
            'cancelUrl' => '/poems/view.php?id=' . $id,
            'poem' => $poem,
            'photos' => $this->photos->all(),
            'categories' => $this->categories->all(),
            'volumes' => (new Volume(get_db()))->all(),
            'errors' => $errors,
        ]);
    }

    public function destroy(?int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Method not allowed.');
        }

        if ($id === null || !$this->poems->delete($id)) {
            flash('error', 'The poem could not be found.');
        } else {
            flash('success', 'Your poem has been deleted.');
        }

        header('Location: /');
        exit;
    }

    private function formData(?int $parentPoemId = null, ?int $existingPhotoId = null): array
    {
        $poem = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'written_date' => trim((string) ($_POST['written_date'] ?? '')),
            'description' => sanitize_html(trim((string) ($_POST['description'] ?? ''))),
            'content' => sanitize_html(trim((string) ($_POST['content'] ?? ''))),
            'parent_poem_id' => $parentPoemId,
            'photo_id' => $existingPhotoId,
            'photo_name' => trim((string) ($_POST['photo_name'] ?? '')),
            'photo_title' => trim((string) ($_POST['photo_title'] ?? '')),
            'photo_alt_text' => trim((string) ($_POST['photo_alt_text'] ?? '')),
        ];
        $categoryIds = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) ($_POST['category_ids'] ?? [])
        ), static fn (int $id): bool => $id > 0)));
        $volumeIds = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) ($_POST['volume_ids'] ?? [])
        ), static fn (int $id): bool => $id > 0)));
        $categories = [];
        $volumes = [];
        $errors = [];

        if ($parentPoemId !== null && $this->poems->find($parentPoemId) === null) {
            $poem['parent_poem_id'] = null;
            $errors[] = 'The original poem for this version could not be found.';
            flash('error', 'The original poem for this version could not be found.');
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $poem['written_date']);
        if ($date === false || $date->format('Y-m-d') !== $poem['written_date']) {
            $errors[] = 'A valid written date is required.';
            flash('error', 'A valid written date is required.');
        }

        $selectedPhotoId = (int) ($_POST['photo_id'] ?? 0);
        if ($selectedPhotoId > 0) {
            if (!$this->photos->exists($selectedPhotoId)) {
                $errors[] = 'The selected photo could not be found.';
                flash('error', 'The selected photo could not be found.');
            } else {
                $poem['photo_id'] = $selectedPhotoId;
            }
        }

        if (isset($_FILES['photo']) && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            try {
                $filename = basename((string) $_FILES['photo']['name']);
                $poem['photo_name'] = $poem['photo_name'] !== '' ? $poem['photo_name'] : $filename;
                if ($poem['photo_title'] === '' || $poem['photo_alt_text'] === '') {
                    throw new RuntimeException('Image title and alt text are required for a new upload.');
                }
                $poem['photo_id'] = $this->photos->store($_FILES['photo'], $poem['photo_name'], $poem['photo_title'], $poem['photo_alt_text']);
            } catch (Throwable $exception) {
                $errors[] = $exception->getMessage();
                flash('error', $exception->getMessage());
            }
        }

        foreach ($categoryIds as $categoryId) {
            if (!$this->categories->exists($categoryId)) {
                $errors[] = 'One or more selected categories could not be found.';
                flash('error', 'One or more selected categories could not be found.');
                break;
            }
            $categories[] = $this->categories->find($categoryId);
        }

        $volumeModel = new Volume(get_db());
        foreach ($volumeIds as $volumeId) {
            if ($volumeModel->find($volumeId) === null) {
                $errors[] = 'One or more selected volumes could not be found.';
                flash('error', 'One or more selected volumes could not be found.');
                break;
            }
            $volumes[] = $volumeModel->find($volumeId);
        }

        if ($poem['title'] === '' || $poem['content'] === '') {
            $errors[] = 'Both a title and content are required.';
            flash('error', 'Both a title and content are required.');
        }

        $poem['categories'] = $categories;
        $poem['categoryIds'] = $categoryIds;
        $poem['volumes'] = $volumes;
        $poem['volumeIds'] = $volumeIds;

        return [$poem, $errors];
    }

    private function findOrFail(?int $id): array
    {
        if ($id === null) {
            http_response_code(404);
            exit('Poem not found.');
        }

        $poem = $this->poems->find($id);
        if ($poem === null) {
            http_response_code(404);
            exit('Poem not found.');
        }

        return $poem;
    }
}
