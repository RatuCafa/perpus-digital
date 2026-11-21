<?php
declare(strict_types=1);

namespace App\Contracts;

/** Kontrak untuk semua media yang dapat disewa/dipinjam. */
interface Loanable
{
    public function getDailyFee(): int;      // tarif sewa per hari (Rp)
    public function getMaxLoanDays(): int;   // batas maksimal lama pinjam
    public function calculateFee(int $days): int;
}
