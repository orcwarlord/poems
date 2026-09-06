<?php

declare(strict_types=1);

class ImageController
{
    public function __construct(private Photo $photos)
    {
    }

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id === false || $id === null || !$this->photos->exists($id)) {
                flash('error', 'The selected image could not be found.');
            } else {
                $this->photos->setDefault($id);
                flash('success', 'The default image has been updated.');
            }
            header('Location: /images/');
            exit;
        }

        render('images/index', [
            'pageTitle' => 'Manage Images',
            'photos' => $this->photos->all(),
        ]);
    }

    public function create(): void
    {
        $photo = [
            'original_name' => '',
            'title' => '',
            'alt_text' => '',
        ];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $photo['original_name'] = trim((string) ($_POST['name'] ?? ''));
            $photo['title'] = trim((string) ($_POST['title'] ?? ''));
            $photo['alt_text'] = trim((string) ($_POST['alt_text'] ?? ''));

            if ($photo['original_name'] === '' && isset($_FILES['photo']['name'])) {
                $photo['original_name'] = basename((string) $_FILES['photo']['name']);
            }

            if (($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                $errors[] = 'An image is required.';
            }
            if ($photo['title'] === '' || $photo['alt_text'] === '') {
                $errors[] = 'Title and alt text are required.';
            }

            if ($errors === []) {
                try {
                    $this->photos->store($_FILES['photo'], $photo['original_name'], $photo['title'], $photo['alt_text']);
                    flash('success', 'The image has been uploaded and is ready to use.');
                    header('Location: /images/');
                    exit;
                } catch (Throwable $exception) {
                    $errors[] = $exception->getMessage();
                    flash('error', $exception->getMessage());
                }
            } else {
                flash('error', implode(' ', $errors));
            }
        }

        render('images/form', [
            'pageTitle' => 'New Image',
            'heading' => 'New Image',
            'photo' => $photo,
            'errors' => $errors,
            'isNew' => true,
        ]);
    }

    public function edit(?int $id): void
    {
        $photo = $this->findOrFail($id);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $photo['original_name'] = trim((string) ($_POST['name'] ?? ''));
            $photo['title'] = trim((string) ($_POST['title'] ?? ''));
            $photo['alt_text'] = trim((string) ($_POST['alt_text'] ?? ''));

            if ($photo['original_name'] === '' || $photo['title'] === '' || $photo['alt_text'] === '') {
                $errors[] = 'Name, title, and alt text are required.';
                flash('error', 'Name, title, and alt text are required.');
            } else {
                $this->photos->update($id, $photo['original_name'], $photo['title'], $photo['alt_text']);
                flash('success', 'The image details have been updated.');
                header('Location: /images/');
                exit;
            }
        }

        render('images/form', [
            'pageTitle' => 'Edit Image',
            'photo' => $photo,
            'errors' => $errors,
        ]);
    }

    private function findOrFail(?int $id): array
    {
        $photo = $id === null ? null : $this->photos->find($id);
        if ($photo === null) {
            http_response_code(404);
            exit('Image not found.');
        }

        return $photo;
    }
}