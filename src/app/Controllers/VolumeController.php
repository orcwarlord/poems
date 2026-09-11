<?php

declare(strict_types=1);

class VolumeController
{
    public function __construct(
        private Volume $volumes,
        private Poem $poems
    ) {
    }

    public function index(): void
    {
        render('volumes/index', [
            'pageTitle' => 'Volumes',
            'volumes' => $this->volumes->all(),
        ]);
    }

    public function create(): void
    {
        $volume = [
            'name' => '',
            'description' => '',
            'poems' => [],
        ];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$volume, $errors] = $this->formData();

            if ($errors === []) {
                $this->volumes->create($volume['name'], $volume['description'], $volume['poemIds']);
                flash('success', 'Your volume has been saved.');
                header('Location: /volumes/');
                exit;
            }
        }

        render('volumes/form', [
            'pageTitle' => 'New Volume',
            'heading' => 'New Volume',
            'submitLabel' => 'Save volume',
            'cancelUrl' => '/volumes/',
            'volume' => $volume,
            'poems' => $this->poems->all(),
            'errors' => $errors,
        ]);
    }

    public function show(?int $id): void
    {
        $volume = $this->findOrFail($id);

        render('volumes/show', [
            'pageTitle' => $volume['name'],
            'volume' => $volume,
        ]);
    }

    public function edit(?int $id): void
    {
        $volume = $this->findOrFail($id);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$formData, $errors] = $this->formData();
            $volume['name'] = $formData['name'];
            $volume['description'] = $formData['description'];
            $volume['poems'] = $formData['poems'];

            if ($errors === []) {
                $this->volumes->update($id, $volume['name'], $volume['description'], $formData['poemIds']);
                flash('success', 'Your volume has been updated.');
                header('Location: /volumes/view.php?id=' . $id);
                exit;
            }
        }

        render('volumes/form', [
            'pageTitle' => 'Edit Volume',
            'heading' => 'Edit Volume',
            'submitLabel' => 'Save changes',
            'cancelUrl' => '/volumes/view.php?id=' . $id,
            'volume' => $volume,
            'poems' => $this->poems->all(),
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

        if ($id === null || !$this->volumes->delete($id)) {
            flash('error', 'The volume could not be found.');
        } else {
            flash('success', 'Your volume has been deleted.');
        }

        header('Location: /volumes/');
        exit;
    }

    private function formData(): array
    {
        $volume = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'description' => sanitize_html(trim((string) ($_POST['description'] ?? ''))),
            'poems' => [],
        ];
        $poemIds = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) ($_POST['poem_ids'] ?? [])
        ), static fn (int $id): bool => $id > 0)));
        $errors = [];

        if ($volume['name'] === '') {
            $errors[] = 'A volume name is required.';
            flash('error', 'A volume name is required.');
        }

        if ($volume['description'] === '') {
            $errors[] = 'A description is required.';
            flash('error', 'A description is required.');
        }

        foreach ($poemIds as $poemId) {
            if ($this->poems->find($poemId) === null) {
                $errors[] = 'One or more selected poems could not be found.';
                flash('error', 'One or more selected poems could not be found.');
                break;
            }
        }

        $volume['poemIds'] = $poemIds;
        $volume['poems'] = array_filter($this->poems->all(), static fn (array $poem): bool => in_array((int) $poem['id'], $poemIds, true));

        return [$volume, $errors];
    }

    private function findOrFail(?int $id): array
    {
        if ($id === null) {
            http_response_code(404);
            exit('Volume not found.');
        }

        $volume = $this->volumes->find($id);
        if ($volume === null) {
            http_response_code(404);
            exit('Volume not found.');
        }

        return $volume;
    }
}
