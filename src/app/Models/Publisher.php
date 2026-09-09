<?php

declare(strict_types=1);

class Publisher
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $publishers = $this->db->query(
            'SELECT id, name, contact, url, telephone, email, created_at, updated_at FROM publishers ORDER BY name'
        )->fetchAll();
        foreach ($publishers as &$publisher) {
            $publisher['contacts'] = $this->contactsFor((int) $publisher['id']);
        }
        unset($publisher);

        return $publishers;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, contact, url, telephone, email, created_at, updated_at FROM publishers WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $publisher = $stmt->fetch();

        if ($publisher === false) {
            return null;
        }

        $publisher['contacts'] = $this->contactsFor($id);

        return $publisher;
    }

    public function create(array $publisher): int
    {
        $contacts = $publisher['contacts'] ?? [];
        unset($publisher['contacts']);
        $stmt = $this->db->prepare(
            'INSERT INTO publishers (name, contact, url, telephone, email)
             VALUES (:name, :contact, :url, :telephone, :email)'
        );
        $stmt->execute($publisher);
        $id = (int) $this->db->lastInsertId();
        $this->syncContacts($id, $contacts);

        return $id;
    }

    public function update(int $id, array $publisher): bool
    {
        $contacts = $publisher['contacts'] ?? [];
        unset($publisher['contacts']);
        $publisher['id'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE publishers
             SET name = :name, contact = :contact, url = :url, telephone = :telephone, email = :email
             WHERE id = :id'
        );
        $stmt->execute($publisher);
        $this->syncContacts($id, $contacts);

        return $stmt->rowCount() === 1;
    }

    private function contactsFor(int $publisherId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, telephone FROM publisher_contacts
             WHERE publisher_id = :publisher_id ORDER BY name, id'
        );
        $stmt->execute(['publisher_id' => $publisherId]);

        return $stmt->fetchAll();
    }

    private function syncContacts(int $publisherId, array $contacts): void
    {
        $stmt = $this->db->prepare('DELETE FROM publisher_contacts WHERE publisher_id = :publisher_id');
        $stmt->execute(['publisher_id' => $publisherId]);
        if ($contacts === []) {
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO publisher_contacts (publisher_id, name, email, telephone)
             VALUES (:publisher_id, :name, :email, :telephone)'
        );
        foreach ($contacts as $contact) {
            $stmt->execute([
                'publisher_id' => $publisherId,
                'name' => $contact['name'],
                'email' => $contact['email'] !== '' ? $contact['email'] : null,
                'telephone' => $contact['telephone'] !== '' ? $contact['telephone'] : null,
            ]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM publishers WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() === 1;
    }
}