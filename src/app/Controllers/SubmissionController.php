<?php

declare(strict_types=1);

class SubmissionController
{
    public function __construct(private Submission $submissions, private Poem $poems, private Publisher $publishers)
    {
    }

    public function index(): void
    {
        render('submissions/index', ['pageTitle' => 'Submissions', 'submissions' => $this->submissions->all()]);
    }

    public function show(?int $id): void
    {
        $submission = $this->findOrFail($id);
        $poem = $this->poems->find((int) $submission['poem_id']);
        $publisher = $submission['publisher_id'] !== null ? $this->publishers->find((int) $submission['publisher_id']) : null;

        render('submissions/show', [
            'pageTitle' => $submission['call_name'],
            'submission' => $submission,
            'poem' => $poem,
            'publisher' => $publisher,
        ]);
    }

    public function create(): void
    {
        $submission = $this->emptySubmission();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$submission, $errors] = $this->formData();
            if ($errors === []) {
                $this->submissions->create($submission);
                flash('success', 'Your submission has been saved.');
                header('Location: /submissions/');
                exit;
            }
        }
        $this->renderForm('New Submission', 'Save submission', '/submissions/', $submission, $errors);
    }

    public function edit(?int $id): void
    {
        $submission = $this->findOrFail($id);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$formData, $errors] = $this->formData();
            $submission = array_merge($submission, $formData);
            if ($errors === []) {
                $this->submissions->update($id, $formData);
                flash('success', 'Your submission has been updated.');
                header('Location: /submissions/');
                exit;
            }
        }
        $this->renderForm('Edit Submission', 'Save changes', '/submissions/', $submission, $errors);
    }

    public function destroy(?int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Method not allowed.');
        }
        if ($id === null || !$this->submissions->delete($id)) {
            flash('error', 'The submission could not be found.');
        } else {
            flash('success', 'Your submission has been deleted.');
        }
        header('Location: /submissions/');
        exit;
    }

    private function emptySubmission(): array
    {
        return ['poem_id' => 0, 'publisher_id' => 0, 'publisher_name' => '', 'publisher_contact_name' => '', 'publisher_contact_id' => null, 'call_name' => '', 'submission_url' => '', 'closing_date' => '', 'submitted_date' => date('Y-m-d')];
    }

    private function formData(): array
    {
        $publisherId = (int) ($_POST['publisher_id'] ?? 0);
        $publisherName = trim((string) ($_POST['publisher_name'] ?? ''));
        $publisherContactName = trim((string) ($_POST['publisher_contact_name'] ?? ''));
        $submission = [
            'poem_id' => (int) ($_POST['poem_id'] ?? 0),
            'publisher_id' => $publisherId > 0 ? $publisherId : null,
            'publisher_name' => $publisherId > 0 ? '' : $publisherName,
            'publisher_contact_name' => $publisherId > 0 ? '' : $publisherContactName,
            'publisher_contact_id' => (int) ($_POST['publisher_contact_id'] ?? 0) ?: null,
            'call_name' => trim((string) ($_POST['call_name'] ?? '')),
            'submission_url' => normalise_url_value((string) ($_POST['submission_url'] ?? '')),
            'closing_date' => trim((string) ($_POST['closing_date'] ?? '')),
            'submitted_date' => trim((string) ($_POST['submitted_date'] ?? '')),
        ];
        $errors = [];
        if ($submission['call_name'] === '') {
            $errors[] = 'A call or competition name is required.';
        }
        if ($this->poems->find($submission['poem_id']) === null) {
            $errors[] = 'A valid poem is required.';
        }
        if ($submission['publisher_id'] !== null) {
            $publisher = $this->publishers->find($submission['publisher_id']);
            if ($publisher === null) {
                $errors[] = 'A valid publisher is required.';
            } elseif ($submission['publisher_contact_id'] !== null) {
                $contactIds = array_column($publisher['contacts'], 'id');
                if (!in_array($submission['publisher_contact_id'], array_map('intval', $contactIds), true)) {
                    $errors[] = 'The selected publisher contact is invalid.';
                }
            }
        } elseif ($submission['publisher_name'] === '') {
            $errors[] = 'A publisher name is required.';
        }
        if ($submission['submission_url'] !== '' && !$this->isValidUrl($submission['submission_url'])) {
            $errors[] = 'A valid submission URL is required.';
        }
        foreach (['closing_date' => 'closing', 'submitted_date' => 'submission'] as $field => $label) {
            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $submission[$field]);
            if ($date === false || $date->format('Y-m-d') !== $submission[$field]) {
                $errors[] = "A valid {$label} date is required.";
            }
        }
        if ($errors !== []) {
            flash('error', 'Please correct the highlighted submission details.');
        }
        return [$submission, $errors];
    }

    private function renderForm(string $heading, string $submitLabel, string $cancelUrl, array $submission, array $errors): void
    {
        render('submissions/form', [
            'pageTitle' => $heading, 'heading' => $heading, 'submitLabel' => $submitLabel,
            'cancelUrl' => $cancelUrl, 'submission' => $submission, 'errors' => $errors,
            'poems' => $this->poems->all(), 'publishers' => $this->publishers->all(),
        ]);
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
        if ($id === null || ($submission = $this->submissions->find($id)) === null) {
            http_response_code(404);
            exit('Submission not found.');
        }
        return $submission;
    }
}