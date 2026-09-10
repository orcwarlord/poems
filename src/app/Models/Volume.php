<?php

declare(strict_types=1);

class Volume
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT v.id, v.name, v.description, v.created_at, v.updated_at,
                    COUNT(vp.poem_id) AS poem_count
             FROM volumes v
             LEFT JOIN volume_poems vp ON vp.volume_id = v.id
             GROUP BY v.id, v.name, v.description, v.created_at, v.updated_at
             ORDER BY v.name ASC'
        );

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, description, created_at, updated_at
             FROM volumes
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $volume = $stmt->fetch();

        if ($volume !== false) {
            $volume['poems'] = $this->poemsFor($id);
        }

        return $volume === false ? null : $volume;
    }

    public function create(string $name, string $description, array $poemIds = []): int
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare(
            'INSERT INTO volumes (name, description) VALUES (:name, :description)'
        );
        $stmt->execute([
            'name' => $name,
            'description' => $description,
        ]);
        $id = (int) $this->db->lastInsertId();
        $this->syncPoems($id, $poemIds);
        $this->db->commit();

        return $id;
    }

    public function update(int $id, string $name, string $description, array $poemIds = []): bool
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare(
            'UPDATE volumes SET name = :name, description = :description WHERE id = :id'
        );
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'id' => $id,
        ]);
        $this->syncPoems($id, $poemIds);
        $this->db->commit();

        return $stmt->rowCount() === 1;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM volumes WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() === 1;
    }

    public function poemsFor(int $volumeId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.id, p.title, p.written_date, p.description
             FROM poems p
             INNER JOIN volume_poems vp ON vp.poem_id = p.id
             WHERE vp.volume_id = :volume_id
             ORDER BY p.title ASC'
        );
        $stmt->execute(['volume_id' => $volumeId]);

        return $stmt->fetchAll();
    }

    public function allPoems(): array
    {
        $stmt = $this->db->query(
            'SELECT id, title, written_date, description
             FROM poems
             ORDER BY title ASC, written_date DESC'
        );

        return $stmt->fetchAll();
    }

    private function syncPoems(int $volumeId, array $poemIds): void
    {
        $stmt = $this->db->prepare('DELETE FROM volume_poems WHERE volume_id = :volume_id');
        $stmt->execute(['volume_id' => $volumeId]);

        if ($poemIds === []) {
            return;
        }

        $insert = $this->db->prepare(
            'INSERT INTO volume_poems (volume_id, poem_id) VALUES (:volume_id, :poem_id)'
        );
        foreach (array_values(array_unique(array_map('intval', $poemIds))) as $poemId) {
            if ($poemId <= 0) {
                continue;
            }
            $insert->execute(['volume_id' => $volumeId, 'poem_id' => $poemId]);
        }
    }
}
