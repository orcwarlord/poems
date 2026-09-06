<?php

declare(strict_types=1);

class Poem
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query(
                'SELECT p.id, p.title, p.description, p.content, p.photo_id,
                    COALESCE(pa.thumbnail_path, dp.thumbnail_path) AS photo_thumbnail_path,
                    COALESCE(pa.original_name, dp.original_name) AS photo_original_name,
                    COALESCE(pa.title, dp.title) AS photo_title,
                    COALESCE(pa.alt_text, dp.alt_text) AS photo_alt_text, p.created_at
             FROM poems p LEFT JOIN photo_assets pa ON pa.id = p.photo_id
                 LEFT JOIN photo_assets dp ON dp.is_default = TRUE
             ORDER BY p.created_at DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
                'SELECT p.id, p.title, p.description, p.content, p.photo_id,
                    COALESCE(pa.original_path, dp.original_path) AS photo_original_path,
                    COALESCE(pa.thumbnail_path, dp.thumbnail_path) AS photo_thumbnail_path,
                    COALESCE(pa.original_name, dp.original_name) AS photo_original_name,
                    COALESCE(pa.title, dp.title) AS photo_title,
                    COALESCE(pa.alt_text, dp.alt_text) AS photo_alt_text,
                    p.created_at, p.updated_at
             FROM poems p LEFT JOIN photo_assets pa ON pa.id = p.photo_id
                 LEFT JOIN photo_assets dp ON dp.is_default = TRUE
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $poem = $stmt->fetch();

        if ($poem !== false) {
            $poem['categories'] = $this->categoriesFor($id);
        }

        return $poem === false ? null : $poem;
    }

    public function create(string $title, string $description, string $content, ?int $photoId, array $categoryIds = []): int
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare(
            'INSERT INTO poems (title, description, content, photo_id)
             VALUES (:title, :description, :content, :photo_id)'
        );
        $stmt->execute(['title' => $title, 'description' => $description, 'content' => $content, 'photo_id' => $photoId]);
        $id = (int) $this->db->lastInsertId();
        $this->syncCategories($id, $categoryIds);
        $this->db->commit();

        return $id;
    }

    public function update(int $id, string $title, string $description, string $content, ?int $photoId, array $categoryIds = []): bool
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare(
            'UPDATE poems SET title = :title, description = :description, content = :content,
             photo_id = :photo_id WHERE id = :id'
        );
        $stmt->execute([
            'title' => $title,
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
