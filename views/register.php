<?php use App\Support\Http; ?>
<section class="narrow">
    <img class="form-logo" src="assets/img/logo.svg" alt="" width="52" height="52">
    <h1>Daftar Member</h1>
    <form method="post" action="index.php?page=register" class="validate" novalidate>
        <input type="hidden" name="csrf" value="<?= Http::csrfToken() ?>">
        <input type="hidden" name="action" value="register">
        <label for="name">Nama</label>
        <input id="name" name="name" type="text" required minlength="3">
        <small class="error" data-for="name"></small>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required autocomplete="username">
        <small class="error" data-for="email"></small>
        <label for="password">Password (min. 6 karakter)</label>
        <input id="password" name="password" type="password" required minlength="6" autocomplete="new-password">
        <small id="strength" class="muted"></small>
        <small class="error" data-for="password"></small>
        <label for="confirm">Konfirmasi password</label>
        <input id="confirm" name="confirm" type="password" required autocomplete="new-password">
        <small class="error" data-for="confirm"></small>
        <button type="submit">Daftar</button>
    </form>
</section>
