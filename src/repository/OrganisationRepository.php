<?php

namespace App\Repository;

use App\Model\Organisation;
use PDO;

class OrganisationRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?Organisation
    {
        $stmt = $this->db->prepare("SELECT * FROM organisations WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Organisation(
            id: (int)$row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            created_at: $row['created_at'],
            updated_at: $row['updated_at']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM organisations");
        $organisations = [];

        while ($row = $stmt->fetch()) {
            $organisations[] = new Organisation(
                id: (int)$row['id'],
                name: $row['name'],
                description: $row['description'] ?? null,
                created_at: $row['created_at'],
                updated_at: $row['updated_at']
            );
        }

        return $organisations;
    }

    public function save(Organisation $org): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO organisations (name, description)
            VALUES (:name, :description)
        ");
        $stmt->execute([
            'name' => $org->name,
            'description' => $org->description
        ]);
    }

    public function update(Organisation $org): void
    {
        $stmt = $this->db->prepare("
            UPDATE organisations SET name = :name, description = :description
            WHERE id = :id
        ");
        $stmt->execute([
            'id' => $org->id,
            'name' => $org->name,
            'description' => $org->description
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM organisations WHERE id = ?");
        $stmt->execute([$id]);
    }
}
