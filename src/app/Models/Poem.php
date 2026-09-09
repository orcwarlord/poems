<?php

declare(strict_types=1);

class Poem
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $poems = $this->db->query(
                'SELECT p.id, p.title, p.written_date, p.description, p.content, p.parent_poem_id, p.photo_id,
                    COALESCE(pa.thumbnail_path, dp.thumbnail_path) AS photo_thumbnail_path,
                    COALESCE(pa.original_name, dp.original_name) AS photo_original_name,
                    COALESCE(pa.title, dp.title) AS photo_title,
                    COALESCE(pa.alt_text, dp.alt_text) AS photo_alt_text, p.created_at
             FROM poems p LEFT JOIN photo_assets pa ON pa.id = p.photo_id
                 LEFT JOIN photo_assets dp ON dp.is_default = TRUE
             ORDER BY p.created_at DESC'
        )->fetchAll();

        $children = [];
        foreach ($poems as $poem) {
            $parentId = $poem['parent_poem_id'] === null ? 0 : (int) $poem['parent_poem_id'];
            $children[$parentId][] = $poem;
        }

        $arranged = [];
        $append = function (array $poem, int $depth) use (&$append, &$arranged, $children): void {
            $poem['depth'] = $depth;
            $poem['version_number'] = $depth + 1;
            $arranged[] = $poem;

            foreach ($children[(int) $poem['id']] ?? [] as $child) {
                $append($child, $depth + 1);
            }
        };

        foreach ($children[0] ?? [] as $poem) {
            $append($poem, 0);
        }

        return $arranged;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
                'SELECT p.id, p.title, p.written_date, p.description, p.content, p.parent_poem_id, p.photo_id,
                    COALESCE(pa.original_path, dp.original_path) AS photo_original_path,
                    COALESCE(pa.thumbnail_path, dp.thumbnail_path) AS photo_thumbnail_path,
                    COALESCE(pa.original_name, dp.original_name) AS photo_original_name,
                    COALESCE(pa.title, dp.title) AS photo_title,
                    COALESCE(pa.alt_text, dp.alt_text) AS photo_alt_text,
                    p.created_at, p.updated_at, parent.title AS parent_title
             FROM poems p LEFT JOIN photo_assets pa ON pa.id = p.photo_id
                 LEFT JOIN photo_assets dp ON dp.is_default = TRUE
                 LEFT JOIN poems parent ON parent.id = p.parent_poem_id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $poem = $stmt->fetch();

        if ($poem !== false) {
            $poem['categories'] = $this->categoriesFor($id);
            $poem['version_number'] = $this->versionNumber($id);
            $poem['versions'] = $this->versionsFor($id);
            $poem['submissions'] = $this->submissionsFor($id);
        }

        return $poem === false ? null : $poem;
    }

    public function create(string $title, string $writtenDate, string $description, string $content, ?int $photoId, ?int $parentPoemId = null, array $categoryIds = []): int
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare(
            'INSERT INTO poems (title, written_date, description, content, parent_poem_id, photo_id)
             VALUES (:title, :written_date, :description, :content, :parent_poem_id, :photo_id)'
        );
        $stmt->execute([
            'title' => $title,
            'written_date' => $writtenDate,
            'description' => $description,
            'content' => $content,
            'parent_poem_id' => $parentPoemId,
            'photo_id' => $photoId,
        ]);
        $id = (int) $this->db->lastInsertId();
        $this->syncCategories($id, $categoryIds);
        $this->db->commit();

        return $id;
    }

    public function update(int $id, string $title, string $writtenDate, string $description, string $content, ?int $photoId, array $categoryIds = []): bool
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare(
            'UPDATE poems SET title = :title, written_date = :written_date, description = :description, content = :content,
             photo_id = :photo_id WHERE id = :id'
        );
        $stmt->execute([
            'title' => $title,
            'written_date' => $writtenDate,
            'description' => $description,
            'content' => $content,
            'photo_id' => $photoId,
            'id' => $id,
        ]);
        $this->syncCategories($id, $categoryIds);
        $this->db->commit();

        return $stmt->rowCount() === 1;
    }

    private function categoriesFor(int $poemId): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.id, c.name FROM categories c
             INNER JOIN poem_categories pc ON pc.category_id = c.id
             WHERE pc.poem_id = :poem_id ORDER BY c.name'
        );
        $stmt->execute(['poem_id' => $poemId]);

        return $stmt->fetchAll();
    }

    private function versionsFor(int $poemId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, title, written_date FROM poems
             WHERE parent_poem_id = :parent_poem_id ORDER BY written_date DESC, created_at DESC'
        );
        $stmt->execute(['parent_poem_id' => $poemId]);
        $versions = $stmt->fetchAll();
        foreach ($versions as &$version) {
            $version['version_number'] = $this->versionNumber((int) $version['id']);
        }
        unset($version);

        return $versions;
    }

    private function submissionsFor(int $poemId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.id, s.call_name, s.submission_url, s.closing_date, s.submitted_date,
                    COALESCE(pub.name, s.publisher_name) AS publisher_name,
                    COALESCE(pc.name, s.publisher_contact_name) AS contact_name
             FROM submissions s
             LEFT JOIN publishers pub ON pub.id = s.publisher_id
             LEFT JOIN publisher_contacts pc ON pc.id = s.publisher_contact_id
             WHERE s.poem_id = :poem_id ORDER BY s.closing_date ASC'
        );
        $stmt->execute(['poem_id' => $poemId]);

        return $stmt->fetchAll();
    }

    private function versionNumber(int $poemId): int
    {
        $versionNumber = 1;
        $currentId = $poemId;
        $visited = [];
        $stmt = $this->db->prepare('SELECT parent_poem_id FROM poems WHERE id = :id');

        while (!isset($visited[$currentId])) {
            $visited[$currentId] = true;
            $stmt->execute(['id' => $currentId]);
            $parentId = $stmt->fetchColumn();
            if ($parentId === false || $parentId === null) {
                break;
            }
            $versionNumber++;
            $currentId = (int) $parentId;
        }

        return $versionNumber;
    }

    private function syncCategories(int $poemId, array $categoryIds): void
    {
        $stmt = $this->db->prepare('DELETE FROM poem_categories WHERE poem_id = :poem_id');
        $stmt->execute(['poem_id' => $poemId]);

        if ($categoryIds === []) {
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO poem_categories (poem_id, category_id) VALUES (:poem_id, :category_id)'
        );
        foreach ($categoryIds as $categoryId) {
            $stmt->execute(['poem_id' => $poemId, 'category_id' => $categoryId]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM poems WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() === 1;
    }
}
