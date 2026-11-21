<?php
declare(strict_types=1);

namespace App\Models;

/** Admin: mengelola katalog, tidak meminjam. */
class Admin extends User
{
    public function getRole(): string { return 'admin'; }
    public function canManageCatalog(): bool { return true; }
}
