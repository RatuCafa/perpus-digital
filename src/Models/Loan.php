<?php
declare(strict_types=1);

namespace App\Models;

class Loan extends Entity
{
    public const FINE_PER_DAY = 1000;

    public function __construct(
        private int $userId,
        private int $mediaId,
        private string $borrowedAt,
        private string $dueAt,
        private int $days,
        private int $fee,
        private ?string $returnedAt = null,
        private int $fine = 0,
        int $id = 0
    ) {
        $this->setId($id);
    }

    public function getUserId(): int { return $this->userId; }
    public function getMediaId(): int { return $this->mediaId; }
    public function getBorrowedAt(): string { return $this->borrowedAt; }
    public function getDueAt(): string { return $this->dueAt; }
    public function getDays(): int { return $this->days; }
    public function getFee(): int { return $this->fee; }
    public function getFine(): int { return $this->fine; }
    public function getReturnedAt(): ?string { return $this->returnedAt; }
    public function isActive(): bool { return $this->returnedAt === null; }

    public function isOverdue(): bool
    {
        return $this->isActive() && date('Y-m-d') > $this->dueAt;
    }

    public function markReturned(): void
    {
        $today = date('Y-m-d');
        $late = (int) max(0, (strtotime($today) - strtotime($this->dueAt)) / 86400);
        $this->returnedAt = $today;
        $this->fine = $late * self::FINE_PER_DAY;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(), 'userId' => $this->userId, 'mediaId' => $this->mediaId,
            'borrowedAt' => $this->borrowedAt, 'dueAt' => $this->dueAt, 'days' => $this->days,
            'fee' => $this->fee, 'returnedAt' => $this->returnedAt, 'fine' => $this->fine,
        ];
    }
}
