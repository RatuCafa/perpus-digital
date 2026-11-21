<?php
declare(strict_types=1);

namespace App\Models;

class Ebook extends Media
{
    public function __construct(
        string $title, string $author, string $category, int $year,
        string $description, int $stock,
        private string $format = 'PDF',
        private int $pages = 0,
        int $id = 0
    ) {
        parent::__construct($title, $author, $category, $year, $description, $stock, $id);
    }

    public function getType(): string { return 'ebook'; }
    public function getTypeLabel(): string { return 'E-Book'; }
    public function getDetail(): string { return $this->format . ' - ' . $this->pages . ' halaman'; }
    public function getDailyFee(): int { return 2000; }
    public function getMaxLoanDays(): int { return 14; }

    protected function extraArray(): array
    {
        return ['format' => $this->format, 'pages' => $this->pages];
    }
}
