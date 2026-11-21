<?php
declare(strict_types=1);

namespace App\Support;

use App\Exceptions\AppException;

/** Helper kecil: escape HTML, flash message, CSRF, redirect. */
final class Http
{
    public static function e(mixed $v): string
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }

    /** Huruf kecil; memakai mbstring bila aktif, jika tidak fallback biasa. */
    public static function lower(string $s): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
    }

    /** Ikon dari sprite SVG di layout. */
    public static function icon(string $name): string
    {
        return '<svg class="icon" aria-hidden="true"><use href="#i-' . self::e($name) . '"/></svg>';
    }

    public static function rupiah(int $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    public static function flash(string $type, string $msg): void
    {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    /** @return array{type:string,msg:string}|null */
    public static function pullFlash(): ?array
    {
        $f = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $f;
    }

    public static function csrfToken(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
    }

    public static function checkCsrf(): void
    {
        $sent = $_POST['csrf'] ?? '';
        if (!is_string($sent) || !hash_equals(self::csrfToken(), $sent)) {
            throw new AppException('Sesi tidak valid, silakan ulangi.');
        }
    }

    public static function redirect(string $page, array $params = []): never
    {
        header('Location: index.php?' . http_build_query(['page' => $page] + $params));
        exit;
    }
}
