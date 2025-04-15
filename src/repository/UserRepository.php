<?php

namespace App\Repository;

use PDO;
use App\Model\User;
use App\Model\Organisation;

class UserRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, o.id AS org_id, o.name AS org_name, o.description AS org_description, 
                    o.created_at AS org_created_at, o.updated_at AS org_updated_at
             FROM users u
             LEFT JOIN organisations o ON u.organisation_id = o.id
             WHERE u.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $organisation = $row['organisation_id'] ? new Organisation(
            (int) $row['org_id'],
            $row['org_name'],
            $row['org_description'],
            $row['org_created_at'],
            $row['org_updated_at']
        ) : null;

        return new User(
            (int) $row['id'],
            $row['username'],
            $row['email'],
            '',
            $row['full_name'],
            (int) $row['is_active'],
            $row['role'],
            $organisation,
            $row['created_at'],
            $row['updated_at']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            "SELECT u.*, o.id AS org_id, o.name AS org_name, o.description AS org_description, 
                    o.created_at AS org_created_at, o.updated_at AS org_updated_at
             FROM users u
             LEFT JOIN organisations o ON u.organisation_id = o.id"
        );

        $users = [];

        while ($row = $stmt->fetch()) {
            $organisation = $row['organisation_id'] ? new Organisation(
                (int) $row['org_id'],
                $row['org_name'],
                $row['org_description'],
                $row['org_created_at'],
                $row['org_updated_at']
            ) : null;

            $users[] = new User(
                (int) $row['id'],
                $row['username'],
                $row['email'],
                '',
                $row['full_name'],
                (int) $row['is_active'],
                $row['role'],
                $organisation,
                $row['created_at'],
                $row['updated_at']
            );
        }

        return $users;
    }

    public function save(User $user): void
    {
        if ($user->id > 0) {
            $stmt = $this->db->prepare(
                "UPDATE users SET
                    username = ?, email = ?, password_hash = ?, full_name = ?, is_active = ?, role = ?, organisation_id = ?, updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->execute([
                $user->username,
                $user->email,
                $user->password_hash,
                $user->full_name,
                $user->is_active,
                $user->role,
                $user->organisation?->id,
                $user->id,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO users (username, email, password_hash, full_name, is_active, role, organisation_id, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );

            $stmt->execute([
                $user->username,
                $user->email,
                $user->password_hash,
                $user->full_name,
                $user->is_active,
                $user->role,
                $user->organisation?->id,
            ]);

            $user->id = (int) $this->db->lastInsertId();
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}
