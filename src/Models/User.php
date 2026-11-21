<?php
declare(strict_types=1);

namespace App\Models;

/** Pengguna dasar. Property private (encapsulation), akses lewat getter/setter. */
class User extends Entity
{
    public function __construct(
        private string $name,
        private string $email,
        private string $passwordHash,
        int $id = 0
    ) {
        $this->setId($id);
    }

    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }

    public function setName(string $name): static
    {
        $this->name = trim($name);
        return $this;
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->passwordHash);
    }

    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }

    // Method yang di-override subclass (polymorphism)
    public function getRole(): string { return 'user'; }
    public function getMaxActiveLoans(): int { return 0; }
    public function canManageCatalog(): bool { return false; }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'role' => $this->getRole(),
            'name' => $this->name,
            'email' => $this->email,
            'passwordHash' => $this->passwordHash,
        ];
    }
}
