<?php
use App\Support\Http;
/** @var ?\App\Models\Media $item */
$a = $item ? $item->toArray() : ['type' => 'ebook', 'title' => '', 'author' => '', 'category' => '', 'year' => date('Y'), 'description' => '', 'stock' => 1, 'format' => 'PDF', 'pages' => 0, 'minutes' => 0];
$id = $item ? $item->getId() : 0;
?>
<section class="narrow">
    <h1><?= $id ? 'Ubah Media' : 'Tambah Media' ?></h1>
    <form method="post" action="index.php?page=admin-form<?= $id ? '&amp;id=' . $id : '' ?>" class="validate" novalidate>
        <input type="hidden" name="csrf" value="<?= Http::csrfToken() ?>">
        <input type="hidden" name="action" value="media-save">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label for="type">Jenis</label>
        <select id="type" name="type">
            <option value="ebook" <?= $a['type'] === 'ebook' ? 'selected' : '' ?>>E-Book</option>
            <option value="audiobook" <?= $a['type'] === 'audiobook' ? 'selected' : '' ?>>Audiobook</option>
        </select>

        <label for="title">Judul</label>
        <input id="title" name="title" required value="<?= Http::e($a['title']) ?>">
        <small class="error" data-for="title"></small>

        <label for="author">Penulis</label>
        <input id="author" name="author" required value="<?= Http::e($a['author']) ?>">
        <small class="error" data-for="author"></small>

        <label for="category">Kategori</label>
        <input id="category" name="category" required value="<?= Http::e($a['category']) ?>">
        <small class="error" data-for="category"></small>

        <label for="year">Tahun terbit</label>
        <input id="year" name="year" type="number" min="1000" max="<?= (int) date('Y') + 1 ?>" required value="<?= (int) $a['year'] ?>">
        <small class="error" data-for="year"></small>

        <label for="stock">Stok</label>
        <input id="stock" name="stock" type="number" min="0" required value="<?= (int) $a['stock'] ?>">
        <small class="error" data-for="stock"></small>

        <label for="description">Deskripsi</label>
        <textarea id="description" name="description" rows="3"><?= Http::e($a['description']) ?></textarea>

        <fieldset data-show="ebook">
            <legend>Detail E-Book</legend>
            <label for="format">Format</label>
            <select id="format" name="format">
                <option <?= ($a['format'] ?? '') === 'PDF' ? 'selected' : '' ?>>PDF</option>
                <option <?= ($a['format'] ?? '') === 'EPUB' ? 'selected' : '' ?>>EPUB</option>
            </select>
            <label for="pages">Jumlah halaman</label>
            <input id="pages" name="pages" type="number" min="0" value="<?= (int) ($a['pages'] ?? 0) ?>">
        </fieldset>
        <fieldset data-show="audiobook">
            <legend>Detail Audiobook</legend>
            <label for="minutes">Durasi (menit)</label>
            <input id="minutes" name="minutes" type="number" min="0" value="<?= (int) ($a['minutes'] ?? 0) ?>">
        </fieldset>

        <div class="actions">
            <button type="submit">Simpan</button>
            <a class="button secondary" href="index.php?page=admin">Batal</a>
        </div>
    </form>
</section>
