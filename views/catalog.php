<?php
use App\Support\{Cover, Http};
/** @var \App\Models\Media[] $items */
$q = (string) ($_GET['q'] ?? '');
$type = (string) ($_GET['type'] ?? '');
?>
<section class="hero">
    <div class="hero-text">
    <h1>Pinjam bacaan, tanpa antre.</h1>
    <p>E-book dan audiobook yang bisa kamu sewa per hari, kapan saja.</p>
    <form class="filter" method="get" action="index.php" role="search">
        <input type="hidden" name="page" value="catalog">
        <label class="sr-only" for="q">Cari</label>
        <input id="q" name="q" type="search" value="<?= Http::e($q) ?>" placeholder="Cari judul, penulis, atau kategori">
        <label class="sr-only" for="type">Jenis</label>
        <select id="type" name="type">
            <option value="">Semua jenis</option>
            <option value="ebook" <?= $type === 'ebook' ? 'selected' : '' ?>>E-Book</option>
            <option value="audiobook" <?= $type === 'audiobook' ? 'selected' : '' ?>>Audiobook</option>
        </select>
        <button type="submit"><?= Http::icon('search') ?> Cari</button>
    </form>
    </div>
    <svg class="hero-art" viewBox="0 0 240 200" aria-hidden="true">
        <ellipse cx="120" cy="184" rx="100" ry="9" fill="#000" opacity=".2"/>
        <rect x="30" y="140" width="170" height="30" rx="4" fill="#2f7fb3"/>
        <rect x="30" y="140" width="12" height="30" fill="#000" opacity=".18"/>
        <rect x="50" y="108" width="150" height="30" rx="4" fill="#b5446e"/>
        <rect x="50" y="108" width="12" height="30" fill="#000" opacity=".18"/>
        <rect x="40" y="76" width="140" height="30" rx="4" fill="#f4b860"/>
        <rect x="40" y="76" width="12" height="30" fill="#000" opacity=".18"/>
        <path d="M68 56v-6a38 38 0 0 1 76 0v6" fill="none" stroke="#fff" stroke-width="7" stroke-linecap="round" transform="translate(0 4)"/>
        <rect x="60" y="54" width="16" height="28" rx="7" fill="#fff"/>
        <rect x="136" y="54" width="16" height="28" rx="7" fill="#fff"/>
        <g stroke="#f4b860" stroke-width="4" stroke-linecap="round"><path d="M180 40v14"/><path d="M192 30v34"/><path d="M204 38v18"/></g>
    </svg>
</section>

<section>
    <p id="result-info" class="muted"><?= count($items) ?> media ditemukan.</p>

    <div class="grid" id="catalog-grid">
    <?php foreach ($items as $m): ?>
        <article class="card card-<?= Http::e($m->getType()) ?>" data-search="<?= Http::e(Http::lower($m->getTitle() . ' ' . $m->getAuthor() . ' ' . $m->getCategory())) ?>">
            <div class="card-top">
                <?= Cover::svg($m) ?>
                <header>
                    <span class="tag"><?= Http::icon($m->getType() === 'audiobook' ? 'headphones' : 'book') ?> <?= Http::e($m->getTypeLabel()) ?></span>
                    <h2><?= Http::e($m->getTitle()) ?></h2>
                    <p class="author"><?= Http::e($m->getAuthor()) ?>, <?= $m->getYear() ?></p>
                </header>
            </div>
            <p class="desc"><?= Http::e($m->getDescription()) ?></p>
            <dl class="meta">
                <div><dt>Kategori</dt><dd><?= Http::e($m->getCategory()) ?></dd></div>
                <div><dt>Format</dt><dd><?= Http::e($m->getDetail()) ?></dd></div>
                <div><dt>Maks. pinjam</dt><dd><?= $m->getMaxLoanDays() ?> hari</dd></div>
            </dl>
            <footer class="card-foot">
                <div>
                    <strong><?= Http::rupiah($m->getDailyFee()) ?></strong><span class="muted"> / hari</span><br>
                    <span class="stock <?= $m->isAvailable() ? '' : 'stock-out' ?>">
                        <?= $m->isAvailable() ? 'Tersedia ' . $m->getStock() : 'Stok habis' ?>
                    </span>
                </div>
                <?php if ($user && $user->getMaxActiveLoans() > 0): ?>
                    <button type="button" class="btn-borrow" <?= $m->isAvailable() ? '' : 'disabled' ?>
                        data-id="<?= $m->getId() ?>" data-title="<?= Http::e($m->getTitle()) ?>"
                        data-fee="<?= $m->getDailyFee() ?>" data-max="<?= $m->getMaxLoanDays() ?>"><?= Http::icon('plus') ?> Pinjam</button>
                <?php elseif (!$user): ?>
                    <a class="button secondary" href="index.php?page=login"><?= Http::icon('login') ?> Masuk</a>
                <?php endif; ?>
            </footer>
        </article>
    <?php endforeach; ?>
    </div>
</section>

<dialog id="borrow-dialog">
    <form method="post" action="index.php?page=catalog" id="borrow-form">
        <h2>Pinjam: <span id="borrow-title"></span></h2>
        <input type="hidden" name="csrf" value="<?= Http::csrfToken() ?>">
        <input type="hidden" name="action" value="borrow">
        <input type="hidden" name="media_id" id="borrow-id">
        <label for="borrow-days">Lama pinjam (hari)</label>
        <input type="number" id="borrow-days" name="days" min="1" value="3" required>
        <p class="total">Total sewa: <strong id="borrow-total">-</strong></p>
        <div class="actions">
            <button type="submit">Konfirmasi</button>
            <button type="button" id="borrow-cancel" class="secondary">Batal</button>
        </div>
    </form>
</dialog>
