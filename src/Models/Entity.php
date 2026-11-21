<?php
declare(strict_types=1);

namespace App\Models;

/** Kelas dasar abstrak untuk semua objek yang disimpan ke JSON. */
abstract class Entity
{
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    /** @return array<string,mixed> */
    abstract public function toArray(): array;
}
