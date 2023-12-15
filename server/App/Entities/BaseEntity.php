<?php

namespace App\Entities;

use Ramsey\Uuid\Uuid;

class BaseEntity
{
    private string $id;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
