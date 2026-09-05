<?php

declare(strict_types=1);

class CategoryController
{
    public function __construct(private Category $categories)
    {
    }

    public function index(): void
    {
        render('categories/index', [
            'pageTitle' => 'Categories',
            'categories' => $this->categories->all(),
        ]);
    }

    public function create(): void
    {
        $category = ['name' => ''];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$category, $errors] = $this->formData();

            if ($errors === []) {
                $this->categories->create($category['name']);
                flash('success', 'Your category has been saved.');
                header('Location: /categories/');
                exit;
            }
        }

        render('categories/form', [
            'pageTitle' => 'New Category',
            'heading' => 'New Category',
            'submitLabel' => 'Save category',
            'cancelUrl' => '/categories/',
            'category' => $category,
            'errors' => $errors,
        ]);
    }

    public function createInline(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
            return;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            http_response_code(422);
            echo json_encode(['error' => 'A category name is required.']);
            return;
        }

        try {
            $id = $this->categories->create($name);
        } catch (PDOException $exception) {
            http_response_code(422);
            echo json_encode(['error' => 'That category already exists.']);
            return;
        }

        echo json_encode(['id' => $id, 'name' => $name]);
    }

    public function show(?int $id): void
    {
        $category = $this->findOrFail($id);

        render('categories/show', [
            'pageTitle' => $category['name'],
            'category' => $category,
        ]);
    }

    public function edit(?int $id): void
    {
        $category = $this->findOrFail($id);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$formData, $errors] = $this->formData();
            $category['name'] = $formData['name'];

            if ($errors === []) {
                $this->categories->update($id, $category['name']);
                flash('success', 'Your category has been updated.');
                header('Location: /categories/view.php?id=' . $id);
                exit;
            }
        }

        render('categories/form', [
            'pageTitle' => 'Edit Category',
            'heading' => 'Edit Category',
            'submitLabel' => 'Save changes',
            'cancelUrl' => '/categories/view.php?id=' . $id,
            'category' => $category,
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

        if ($id === null || !$this->categories->delete($id)) {
            flash('error', 'The category could not be found.');
        } else {
            flash('success', 'Your category has been deleted.');
        }

        header('Location: /categories/');
        exit;
    }

    private function formData(): array
    {
        $category = ['name' => trim((string) ($_POST['name'] ?? ''))];
        $errors = [];

        if ($category['name'] === '') {
            $errors[] = 'A category name is required.';
            flash('error', 'A category name is required.');
        }

        return [$category, $errors];
    }

    private function findOrFail(?int $id): array
    {
        if ($id === null) {
            http_response_code(404);
            exit('Category not found.');
        }

        $category = $this->categories->find($id);
        if ($category === null) {
            http_response_code(404);
            exit('Category not found.');
        }

        return $category;
    }
}