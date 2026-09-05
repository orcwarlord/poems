<?php

declare(strict_types=1);

class Category
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT id, name, created_at, updated_at FROM categories ORDER BY name'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, created_at, updated_at FROM categories WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $category = $stmt->fetch();

        return $category === false ? null : $category;
    }

    public function create(string $name): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name) VALUES (:name)'
        );
        $stmt->execute(['name' => $name]);

        return (int) $this->db->lastInsertId();
    }

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetchColumn() !== false;
    }

    public function update(int $id, string $name): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE categories SET name = :name WHERE id = :id'
        );
        $stmt->execute(['name' => $name, 'id' => $id]);

        return $stmt->rowCount() === 1;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() === 1;
    }
}