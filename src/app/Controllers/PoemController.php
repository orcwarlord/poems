<?php

declare(strict_types=1);

class PoemController
{
    public function __construct(private Poem $poems, private Category $categories)
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
        $poem = ['title' => '', 'content' => '', 'categories' => []];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$poem, $errors] = $this->formData();

            if ($errors === []) {
                $this->poems->create($poem['title'], $poem['content'], $poem['categoryIds']);
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
            $poem['content'] = $formData['content'];
            $poem['categories'] = $formData['categories'];

            if ($errors === []) {
                $this->poems->update($id, $poem['title'], $poem['content'], $formData['categoryIds']);
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
            'content' => sanitize_html(trim((string) ($_POST['content'] ?? ''))),
        ];
        $categoryIds = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) ($_POST['category_ids'] ?? [])
        ), static fn (int $id): bool => $id > 0)));
        $categories = [];
        $errors = [];

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
