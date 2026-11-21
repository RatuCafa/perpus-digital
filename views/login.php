<?php use App\Support\Http; ?>
<section class="narrow">
    <img class="form-logo" src="assets/img/logo.svg" alt="" width="52" height="52">
    <h1>Masuk</h1>
    <form method="post" action="index.php?page=login" class="validate" novalidate>
        <input type="hidden" name="csrf" value="<?= Http::csrfToken() ?>">
        <input type="hidden" name="action" value="login">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required autocomplete="username">
        <small class="error" data-for="email"></small>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password">
        <small class="error" data-for="password"></small>
        <button type="submit">Masuk</button>
    </form>
    <p class="muted">Akun admin demo: admin@pustaka.test / admin123</p>
</section>
