<?php

declare(strict_types=1);

class Submission
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT s.id, s.call_name, s.submission_url, s.closing_date, s.submitted_date,
                    s.poem_id, p.title AS poem_title, s.publisher_id,
                    COALESCE(pub.name, s.publisher_name) AS publisher_name,
                    s.publisher_contact_id, COALESCE(pc.name, s.publisher_contact_name) AS contact_name
             FROM submissions s
             INNER JOIN poems p ON p.id = s.poem_id
             LEFT JOIN publishers pub ON pub.id = s.publisher_id
             LEFT JOIN publisher_contacts pc ON pc.id = s.publisher_contact_id
             ORDER BY s.closing_date DESC, s.created_at DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM submissions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $submission = $stmt->fetch();

        return $submission === false ? null : $submission;
    }

    public function create(array $submission): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO submissions (poem_id, publisher_id, publisher_name, publisher_contact_name, publisher_contact_id, call_name, submission_url, closing_date, submitted_date)
             VALUES (:poem_id, :publisher_id, :publisher_name, :publisher_contact_name, :publisher_contact_id, :call_name, :submission_url, :closing_date, :submitted_date)'
        );
        $stmt->execute($submission);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $submission): bool
    {
        $submission['id'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE submissions SET poem_id = :poem_id, publisher_id = :publisher_id, publisher_name = :publisher_name,
             publisher_contact_name = :publisher_contact_name, publisher_contact_id = :publisher_contact_id, call_name = :call_name,
             submission_url = :submission_url, closing_date = :closing_date, submitted_date = :submitted_date
             WHERE id = :id'
        );
        $stmt->execute($submission);

        return $stmt->rowCount() === 1;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM submissions WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() === 1;
    }
}