<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\{Member, User};
use App\Repositories\UserRepository;

class AuthService
{
    public function __construct(private UserRepository $users) {}

    public function current(): ?User
    {
        $id = $_SESSION['user_id'] ?? null;
        $u = $id ? $this->users->find((int) $id) : null;
        return $u instanceof User ? $u : null;
    }

    public function register(string $name, string $email, string $password, string $confirm): Member
    {
        $name = trim($name);
        $email = strtolower(trim($email));
        if (strlen($name) < 3) {
            throw new AppException('Nama minimal 3 karakter.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AppException('Format email tidak valid.');
        }
        if (strlen($password) < 6) {
            throw new AppException('Password minimal 6 karakter.');
        }
        if ($password !== $confirm) {
            throw new AppException('Konfirmasi password tidak cocok.');
        }
        if ($this->users->findByEmail($email)) {
            throw new AppException('Email sudah terdaftar.');
        }
        $member = new Member($name, $email, User::hashPassword($password));
        $this->users->save($member);
        return $member;
    }

    public function login(string $email, string $password): User
    {
        $user = $this->users->findByEmail(trim($email));
        if (!$user || !$user->verifyPassword($password)) {
            throw new AppException('Email atau password salah.');
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        return $user;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
