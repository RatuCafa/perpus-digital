<?php
declare(strict_types=1);

namespace App\Models;

class Audiobook extends Media
{
    public function __construct(
        string $title, string $author, string $category, int $year,
        string $description, int $stock,
        private int $minutes = 0,
        int $id = 0
    ) {
        parent::__construct($title, $author, $category, $year, $description, $stock, $id);
    }

    public function getType(): string { return 'audiobook'; }
    public function getTypeLabel(): string { return 'Audiobook'; }
    public function getDetail(): string { return 'Durasi ' . $this->minutes . ' menit'; }
    public function getDailyFee(): int { return 3000; }   // tarif beda (polymorphism)
    public function getMaxLoanDays(): int { return 7; }   // batas beda

    protected function extraArray(): array
    {
        return ['minutes' => $this->minutes];
    }
}
