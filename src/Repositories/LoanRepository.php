<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\{Entity, Loan};

class LoanRepository extends JsonRepository
{
    protected function hydrate(array $r): Entity
    {
        return new Loan((int) $r['userId'], (int) $r['mediaId'], $r['borrowedAt'], $r['dueAt'],
            (int) $r['days'], (int) $r['fee'], $r['returnedAt'] ?? null, (int) ($r['fine'] ?? 0), (int) $r['id']);
    }

    /** @return Loan[] */
    public function byUser(int $userId): array
    {
        return array_values(array_filter($this->all(), fn(Loan $l) => $l->getUserId() === $userId));
    }

    /** @return Loan[] */
    public function activeByMedia(int $mediaId): array
    {
        return array_values(array_filter($this->all(),
            fn(Loan $l) => $l->getMediaId() === $mediaId && $l->isActive()));
    }
}
