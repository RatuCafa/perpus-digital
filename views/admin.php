<?php
use App\Support\Http;
/** @var \App\Models\Media[] $items */
/** @var \App\Models\Loan[] $loans */
/** @var array<int,string> $userNames */
/** @var array<int,\App\Models\Media> $mediaLookup */
?>
<section>
    <h1><?= Http::icon('grid') ?> Kelola Katalog</h1>
    <p><a class="button" href="index.php?page=admin-form"><?= Http::icon('plus') ?> Tambah media</a></p>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Judul</th><th>Jenis</th><th>Penulis</th><th>Stok</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($items as $m): ?>
            <tr>
                <td><?= Http::e($m->getTitle()) ?></td>
                <td><span class="tag"><?= Http::icon($m->getType() === 'audiobook' ? 'headphones' : 'book') ?> <?= Http::e($m->getTypeLabel()) ?></span></td>
                <td><?= Http::e($m->getAuthor()) ?></td>
                <td><?= $m->getStock() ?></td>
                <td class="row-actions">
                    <a href="index.php?page=admin-form&amp;id=<?= $m->getId() ?>"><?= Http::icon('edit') ?> Ubah</a>
                    <form method="post" action="index.php?page=admin" class="confirm-delete" data-title="<?= Http::e($m->getTitle()) ?>">
                        <input type="hidden" name="csrf" value="<?= Http::csrfToken() ?>">
                        <input type="hidden" name="action" value="media-delete">
                        <input type="hidden" name="id" value="<?= $m->getId() ?>">
                        <button type="submit" class="secondary"><?= Http::icon('trash') ?> Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</section>

<section>
    <h2>Semua Transaksi</h2>
    <?php if (!$loans): ?>
        <p>Belum ada transaksi.</p>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Member</th><th>Media</th><th>Pinjam</th><th>Jatuh tempo</th><th>Biaya</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($loans as $l): $m = $mediaLookup[$l->getMediaId()] ?? null; ?>
            <tr>
                <td><?= Http::e($userNames[$l->getUserId()] ?? '-') ?></td>
                <td><?= Http::e($m ? $m->getTitle() : '(dihapus)') ?></td>
                <td><?= $l->getBorrowedAt() ?></td>
                <td><?= $l->getDueAt() ?></td>
                <td><?= Http::rupiah($l->getFee() + $l->getFine()) ?></td>
                <td><?= $l->isActive() ? ($l->isOverdue() ? 'Terlambat' : 'Dipinjam') : 'Dikembalikan' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</section>
