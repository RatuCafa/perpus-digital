<?php
use App\Support\Http;
/** @var \App\Models\Loan[] $loans */
/** @var array<int,\App\Models\Media> $mediaLookup */
?>
<section>
    <h1><?= Http::icon('clock') ?> Pinjaman Saya</h1>
    <?php if (!$loans): ?>
        <div class="empty">
            <?= Http::icon('book') ?>
            <p>Belum ada riwayat peminjaman.</p>
            <a class="button" href="index.php?page=catalog">Lihat katalog</a>
        </div>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Media</th><th>Pinjam</th><th>Jatuh tempo</th><th>Biaya</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($loans as $l): $m = $mediaLookup[$l->getMediaId()] ?? null; ?>
            <tr>
                <td><?= Http::e($m ? $m->getTitle() : '(dihapus)') ?></td>
                <td><?= $l->getBorrowedAt() ?></td>
                <td><?= $l->getDueAt() ?></td>
                <td><?= Http::rupiah($l->getFee()) ?></td>
                <td>
                    <?php if ($l->isActive()): ?>
                        <?= $l->isOverdue() ? 'Terlambat' : 'Dipinjam' ?>
                    <?php else: ?>
                        Dikembalikan <?= $l->getReturnedAt() ?>
                        <?= $l->getFine() > 0 ? '(denda ' . Http::rupiah($l->getFine()) . ')' : '' ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($l->isActive()): ?>
                    <form method="post" action="index.php?page=loans">
                        <input type="hidden" name="csrf" value="<?= Http::csrfToken() ?>">
                        <input type="hidden" name="action" value="return">
                        <input type="hidden" name="loan_id" value="<?= $l->getId() ?>">
                        <button type="submit"><?= Http::icon('undo') ?> Kembalikan</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</section>
