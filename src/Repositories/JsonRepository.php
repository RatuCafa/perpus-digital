<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Entity;

/** Repository generik berbasis berkas JSON. */
abstract class JsonRepository
{
    public function __construct(protected string $file)
    {
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0775, true);
        }
        if (!is_file($file)) {
            file_put_contents($file, '[]');
        }
    }

    /** @param array<string,mixed> $row */
    abstract protected function hydrate(array $row): Entity;

    /** @return array<int,array<string,mixed>> */
    protected function readRaw(): array
    {
        $data = json_decode((string) file_get_contents($this->file), true);
        return is_array($data) ? $data : [];
    }

    /** @param array<int,array<string,mixed>> $rows */
    protected function writeRaw(array $rows): void
    {
        file_put_contents(
            $this->file,
            json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    /** @return Entity[] */
    public function all(): array
    {
        return array_map(fn(array $r) => $this->hydrate($r), $this->readRaw());
    }

    public function find(int $id): ?Entity
    {
        foreach ($this->readRaw() as $row) {
            if ((int) $row['id'] === $id) {
                return $this->hydrate($row);
            }
        }
        return null;
    }

    public function save(Entity $entity): Entity
    {
        $rows = $this->readRaw();
        if ($entity->getId() === 0) {
            $max = 0;
            foreach ($rows as $r) { $max = max($max, (int) $r['id']); }
            $entity->setId($max + 1);
            $rows[] = $entity->toArray();
        } else {
            $found = false;
            foreach ($rows as $i => $r) {
                if ((int) $r['id'] === $entity->getId()) {
                    $rows[$i] = $entity->toArray();
                    $found = true;
                }
            }
            if (!$found) { $rows[] = $entity->toArray(); }
        }
        $this->writeRaw($rows);
        return $entity;
    }

    public function delete(int $id): void
    {
        $this->writeRaw(array_filter($this->readRaw(), fn($r) => (int) $r['id'] !== $id));
    }
}
