<?php

namespace App\Model;

class Organisation
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $created_at,
        public string $updated_at
    ) {}
}
