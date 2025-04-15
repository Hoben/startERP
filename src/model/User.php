<?php

namespace App\Model;

class User
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public string $password_hash,
        public ?string $full_name,
        public int $is_active,
        public string $role,
        public ?Organisation $organisation,
        public string $created_at,
        public string $updated_at
    ) {}
}
