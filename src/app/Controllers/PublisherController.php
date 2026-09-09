<?php

declare(strict_types=1);

class PublisherController
{
    public function __construct(private Publisher $publishers)
    {
    }

    public function index(): void
    {
        render('publishers/index', [
            'pageTitle' => 'Publishers',
            'publishers' => $this->publishers->all(),
        ]);
    }

    public function create(): void
    {
        $publisher = $this->emptyPublisher();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$publisher, $errors] = $this->formData();

            if ($errors === []) {
                $this->publishers->create($publisher);
                flash('success', 'Your publisher has been saved.');
                header('Location: /publishers/');
                exit;
            }
        }

        render('publishers/form', [
            'pageTitle' => 'New Publisher',
            'heading' => 'New Publisher',
            'submitLabel' => 'Save publisher',
            'cancelUrl' => '/publishers/',
            'publisher' => $publisher,
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

        $publisher = $this->formData();
        [$publisher, $errors] = $publisher;
        if ($errors !== []) {
            http_response_code(422);
            echo json_encode(['error' => $errors[0]]);
            return;
        }

        try {
            $id = $this->publishers->create($publisher);
        } catch (Throwable $exception) {
            http_response_code(422);
            echo json_encode(['error' => 'That publisher could not be created.']);
            return;
        }

        echo json_encode([
            'id' => $id,
            'name' => $publisher['name'],
            'contact' => $publisher['contact'] ?? '',
            'email' => $publisher['email'] ?? '',
            'telephone' => $publisher['telephone'] ?? '',
            'url' => $publisher['url'] ?? '',
        ]);
    }

    public function show(?int $id): void
    {
        $publisher = $this->findOrFail($id);

        render('publishers/show', [
            'pageTitle' => $publisher['name'],
            'publisher' => $publisher,
        ]);
    }

    public function edit(?int $id): void
    {
        $publisher = $this->findOrFail($id);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$formData, $errors] = $this->formData();
            $publisher = array_merge($publisher, $formData);

            if ($errors === []) {
                $this->publishers->update($id, $formData);
                flash('success', 'Your publisher has been updated.');
                header('Location: /publishers/view.php?id=' . $id);
                exit;
            }
        }

        render('publishers/form', [
            'pageTitle' => 'Edit Publisher',
            'heading' => 'Edit Publisher',
            'submitLabel' => 'Save changes',
            'cancelUrl' => '/publishers/view.php?id=' . $id,
            'publisher' => $publisher,
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

        if ($id === null || !$this->publishers->delete($id)) {
            flash('error', 'The publisher could not be found.');
        } else {
            flash('success', 'Your publisher has been deleted.');
        }

        header('Location: /publishers/');
        exit;
    }

    private function emptyPublisher(): array
    {
        return [
            'name' => '',
            'contact' => '',
            'url' => '',
            'telephone' => '',
            'email' => '',
            'contacts' => [],
        ];
    }

    private function formData(): array
    {
        $publisher = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'contact' => trim((string) ($_POST['contact'] ?? '')),
            'url' => normalise_url_value((string) ($_POST['url'] ?? '')),
            'telephone' => trim((string) ($_POST['telephone'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'contacts' => $this->contactData(),
        ];
        $errors = [];

        if ($publisher['name'] === '') {
            $errors[] = 'A publisher name is required.';
        }
        if ($publisher['url'] !== '' && !$this->isValidUrl($publisher['url'])) {
            $errors[] = 'A valid URL is required.';
        }
        if ($publisher['email'] !== '' && filter_var($publisher['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'A valid email address is required.';
        }
        foreach ($publisher['contacts'] as $contact) {
            if ($contact['email'] !== '' && filter_var($contact['email'], FILTER_VALIDATE_EMAIL) === false) {
                $errors[] = 'Each contact must have a valid email address.';
                break;
            }
        }

        if ($errors !== []) {
            flash('error', 'Please correct the highlighted publisher details.');
        }

        return [$publisher, $errors];
    }

    private function contactData(): array
    {
        $contacts = [];
        foreach ((array) ($_POST['contacts'] ?? []) as $contact) {
            $name = trim((string) ($contact['name'] ?? ''));
            $email = trim((string) ($contact['email'] ?? ''));
            $telephone = trim((string) ($contact['telephone'] ?? ''));
            if ($name !== '' || $email !== '' || $telephone !== '') {
                $contacts[] = ['name' => $name, 'email' => $email, 'telephone' => $telephone];
            }
        }

        return $contacts;
    }

    private function isValidUrl(string $value): bool
    {
        $trimmed = normalise_url_value($value);
        if ($trimmed === '') {
            return true;
        }

        return filter_var($trimmed, FILTER_VALIDATE_URL) !== false;
    }

    private function findOrFail(?int $id): array
    {
        if ($id === null || ($publisher = $this->publishers->find($id)) === null) {
            http_response_code(404);
            exit('Publisher not found.');
        }

        return $publisher;
    }
}