<?php
declare(strict_types=1);

namespace App\Models;

/** Member biasa: boleh meminjam maksimal 3 media sekaligus. */
class Member extends User
{
    public function getRole(): string { return 'member'; }
    public function getMaxActiveLoans(): int { return 3; }
}
