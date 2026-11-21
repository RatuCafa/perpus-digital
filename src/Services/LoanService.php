<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\{Loan, Member, User};
use App\Repositories\{LoanRepository, MediaRepository};

class LoanService
{
    public function __construct(private LoanRepository $loans, private MediaRepository $media) {}

    public function borrow(User $user, int $mediaId, int $days): Loan
    {
        if ($user->getMaxActiveLoans() === 0) {
            throw new AppException('Akun ini tidak dapat meminjam media.');
        }
        $media = $this->media->find($mediaId);
        if (!$media instanceof \App\Models\Media) {
            throw new AppException('Media tidak ditemukan.');
        }
        if (!$media->isAvailable()) {
            throw new AppException('Stok media habis.');
        }
        if ($days < 1 || $days > $media->getMaxLoanDays()) {
            throw new AppException('Lama pinjam harus 1 sampai ' . $media->getMaxLoanDays() . ' hari.');
        }

        $active = array_filter($this->loans->byUser($user->getId()), fn(Loan $l) => $l->isActive());
        if (count($active) >= $user->getMaxActiveLoans()) {
            throw new AppException('Batas pinjaman aktif tercapai (' . $user->getMaxActiveLoans() . ').');
        }
        foreach ($active as $l) {
            if ($l->getMediaId() === $mediaId) {
                throw new AppException('Anda masih meminjam media ini.');
            }
        }

        $loan = new Loan($user->getId(), $mediaId, date('Y-m-d'),
            date('Y-m-d', strtotime("+{$days} days")), $days, $media->calculateFee($days));
        $this->loans->save($loan);
        $media->decreaseStock();
        $this->media->save($media);
        return $loan;
    }

    public function giveBack(User $user, int $loanId): Loan
    {
        $loan = $this->loans->find($loanId);
        if (!$loan instanceof Loan || $loan->getUserId() !== $user->getId()) {
            throw new AppException('Data peminjaman tidak ditemukan.');
        }
        if (!$loan->isActive()) {
            throw new AppException('Media sudah dikembalikan.');
        }
        $loan->markReturned();
        $this->loans->save($loan);
        $media = $this->media->find($loan->getMediaId());
        if ($media instanceof \App\Models\Media) {
            $media->increaseStock();
            $this->media->save($media);
        }
        return $loan;
    }

    /** @return Loan[] riwayat terbaru dulu */
    public function history(?User $user = null): array
    {
        $list = $user ? $this->loans->byUser($user->getId()) : $this->loans->all();
        usort($list, fn(Loan $a, Loan $b) => $b->getId() <=> $a->getId());
        return $list;
    }
}
