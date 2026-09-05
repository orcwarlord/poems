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
            'SELECT id, title, content, created_at FROM poems ORDER BY created_at DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, title, content, created_at, updated_at FROM poems WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $poem = $stmt->fetch();

        if ($poem !== false) {
            $poem['categories'] = $this->categoriesFor($id);
        }

        return $poem === false ? null : $poem;
    }

    public function create(string $title, string $content, array $categoryIds = []): int
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare(
            'INSERT INTO poems (title, content) VALUES (:title, :content)'
        );
        $stmt->execute(['title' => $title, 'content' => $content]);
        $id = (int) $this->db->lastInsertId();
        $this->syncCategories($id, $categoryIds);
        $this->db->commit();

        return $id;
    }

    public function update(int $id, string $title, string $content, array $categoryIds = []): bool
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare(
            'UPDATE poems SET title = :title, content = :content WHERE id = :id'
        );
        $stmt->execute(['title' => $title, 'content' => $content, 'id' => $id]);
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
