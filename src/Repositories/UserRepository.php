<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\{Admin, Entity, Member, User};

class UserRepository extends JsonRepository
{
    protected function hydrate(array $row): Entity
    {
        $class = match ($row['role'] ?? 'member') {
            'admin' => Admin::class,
            default => Member::class,
        };
        return new $class($row['name'], $row['email'], $row['passwordHash'], (int) $row['id']);
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->all() as $u) {
            if (strcasecmp($u->getEmail(), $email) === 0) {
                return $u;
            }
        }
        return null;
    }
}
