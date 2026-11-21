<?php use App\Support\Http; /** @var ?\App\Models\User $user */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Http::e($title) ?> - Pustaka Digital</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
        <symbol id="i-book" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></symbol>
        <symbol id="i-headphones" viewBox="0 0 24 24"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-login" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></symbol>
        <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></symbol>
        <symbol id="i-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></symbol>
        <symbol id="i-trash" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></symbol>
        <symbol id="i-undo" viewBox="0 0 24 24"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></symbol>
        <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
    </defs>
</svg>

<header class="site-header">
    <a class="brand" href="index.php">
        <img src="assets/img/logo.svg" alt="" width="34" height="34">
        <span>Pustaka Digital</span>
    </a>
    <button class="nav-toggle" type="button" aria-label="Menu" aria-expanded="false">Menu</button>
    <nav class="main-nav" aria-label="Navigasi utama">
        <a href="index.php?page=catalog"><?= Http::icon('book') ?> Katalog</a>
        <?php if ($user): ?>
            <?php if ($user->canManageCatalog()): ?>
                <a href="index.php?page=admin"><?= Http::icon('grid') ?> Kelola</a>
            <?php else: ?>
                <a href="index.php?page=loans"><?= Http::icon('clock') ?> Pinjaman Saya</a>
            <?php endif; ?>
            <span class="who"><?= Http::icon($user->canManageCatalog() ? 'shield' : 'user') ?> <?= Http::e($user->getName()) ?></span>
            <a href="index.php?page=logout"><?= Http::icon('logout') ?> Keluar</a>
        <?php else: ?>
            <a href="index.php?page=login"><?= Http::icon('login') ?> Masuk</a>
            <a class="nav-cta" href="index.php?page=register">Daftar</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <?php if ($flash): ?>
        <p class="flash <?= $flash['type'] === 'ok' ? 'flash-ok' : 'flash-err' ?>" role="alert">
            <?= Http::icon($flash['type'] === 'ok' ? 'check' : 'shield') ?> <?= Http::e($flash['msg']) ?>
        </p>
    <?php endif; ?>
    <?= $content ?>
</main>
<script src="assets/js/app.js"></script>
</body>
</html>
