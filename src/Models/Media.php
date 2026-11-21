<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\Loanable;
use App\Exceptions\AppException;

/** Kelas abstrak media digital. Subclass menentukan tarif, batas hari, dan detail. */
abstract class Media extends Entity implements Loanable
{
    public function __construct(
        private string $title,
        private string $author,
        private string $category,
        private int $year,
        private string $description,
        private int $stock,
        int $id = 0
    ) {
        $this->setId($id);
    }

    abstract public function getType(): string;     // 'ebook' | 'audiobook'
    abstract public function getTypeLabel(): string;
    abstract public function getDetail(): string;   // info khusus tiap jenis
    abstract public function getDailyFee(): int;
    abstract public function getMaxLoanDays(): int;

    public function calculateFee(int $days): int
    {
        return $this->getDailyFee() * $days;
    }

    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getCategory(): string { return $this->category; }
    public function getYear(): int { return $this->year; }
    public function getDescription(): string { return $this->description; }
    public function getStock(): int { return $this->stock; }
    public function isAvailable(): bool { return $this->stock > 0; }

    public function decreaseStock(): void
    {
        if ($this->stock <= 0) {
            throw new AppException('Stok habis.');
        }
        $this->stock--;
    }

    public function increaseStock(): void
    {
        $this->stock++;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'title' => $this->title,
            'author' => $this->author,
            'category' => $this->category,
            'year' => $this->year,
            'description' => $this->description,
            'stock' => $this->stock,
        ] + $this->extraArray();
    }

    /** @return array<string,mixed> */
    abstract protected function extraArray(): array;
}
