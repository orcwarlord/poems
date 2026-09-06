<?php

declare(strict_types=1);

class PoemController
{
    public function __construct(private Poem $poems, private Category $categories, private Photo $photos)
    {
    }

    public function index(): void
    {
        $poems = $this->poems->all();
        $categories = $this->categories->all();

        render('dashboard', [
            'pageTitle' => 'Dashboard',
            'poems' => $poems,
            'total' => count($poems),
            'latestDate' => $poems[0]['created_at'] ?? null,
            'categories' => $categories,
            'totalCategories' => count($categories),
        ]);
    }

    public function create(): void
    {
        $poem = [
            'title' => '',
            'description' => '',
            'content' => '',
            'photo_id' => null,
            'photo_name' => '',
            'photo_title' => '',
            'photo_alt_text' => '',
            'categories' => [],
        ];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$poem, $errors] = $this->formData();

            if ($errors === []) {
                $this->poems->create($poem['title'], $poem['description'], $poem['content'], $poem['photo_id'], $poem['categoryIds']);
                flash('success', 'Your poem has been saved.');
                header('Location: /');
                exit;
            }
        }

        render('poems/form', [
            'pageTitle' => 'New Poem',
            'heading' => 'New Poem',
            'submitLabel' => 'Save poem',
            'cancelUrl' => '/',
            'poem' => $poem,
            'photos' => $this->photos->all(),
            'categories' => $this->categories->all(),
            'errors' => $errors,
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
            [$formData, $errors] = $this->formData();
            $poem['title'] = $formData['title'];
            $poem['description'] = $formData['description'];
            $poem['content'] = $formData['content'];
            $poem['photo_id'] = $formData['photo_id'];
            $poem['photo_name'] = $formData['photo_name'];
            $poem['photo_title'] = $formData['photo_title'];
            $poem['photo_alt_text'] = $formData['photo_alt_text'];
            $poem['categories'] = $formData['categories'];

            if ($errors === []) {
                $this->poems->update($id, $poem['title'], $poem['description'], $poem['content'], $poem['photo_id'], $formData['categoryIds']);
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

    private function formData(): array
    {
        $poem = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'description' => sanitize_html(trim((string) ($_POST['description'] ?? ''))),
            'content' => sanitize_html(trim((string) ($_POST['content'] ?? ''))),
            'photo_id' => null,
            'photo_name' => trim((string) ($_POST['photo_name'] ?? '')),
            'photo_title' => trim((string) ($_POST['photo_title'] ?? '')),
            'photo_alt_text' => trim((string) ($_POST['photo_alt_text'] ?? '')),
        ];
        $categoryIds = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) ($_POST['category_ids'] ?? [])
        ), static fn (int $id): bool => $id > 0)));
        $categories = [];
        $errors = [];

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

        if ($poem['title'] === '' || $poem['content'] === '') {
            $errors[] = 'Both a title and content are required.';
            flash('error', 'Both a title and content are required.');
        }

        $poem['categories'] = $categories;
        $poem['categoryIds'] = $categoryIds;

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
