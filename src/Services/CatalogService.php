<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\{Audiobook, Ebook, Media};
use App\Support\Http;
use App\Repositories\{LoanRepository, MediaRepository};

class CatalogService
{
    public function __construct(private MediaRepository $media, private LoanRepository $loans) {}

    /** @return Media[] */
    public function search(string $q = '', string $type = ''): array
    {
        $q = Http::lower(trim($q));
        return array_values(array_filter($this->media->all(), function (Media $m) use ($q, $type) {
            if ($type !== '' && $m->getType() !== $type) { return false; }
            if ($q === '') { return true; }
            return str_contains(Http::lower($m->getTitle() . ' ' . $m->getAuthor() . ' ' . $m->getCategory()), $q);
        }));
    }

    public function find(int $id): ?Media
    {
        $m = $this->media->find($id);
        return $m instanceof Media ? $m : null;
    }

    /** @param array<string,mixed> $in */
    public function save(array $in, int $id = 0): Media
    {
        $title = trim((string) ($in['title'] ?? ''));
        $author = trim((string) ($in['author'] ?? ''));
        $category = trim((string) ($in['category'] ?? ''));
        $year = (int) ($in['year'] ?? 0);
        $desc = trim((string) ($in['description'] ?? ''));
        $stock = (int) ($in['stock'] ?? -1);
        $type = (string) ($in['type'] ?? 'ebook');

        if ($title === '' || $author === '' || $category === '') {
            throw new AppException('Judul, penulis, dan kategori wajib diisi.');
        }
        if ($year < 1000 || $year > (int) date('Y') + 1) {
            throw new AppException('Tahun terbit tidak valid.');
        }
        if ($stock < 0) {
            throw new AppException('Stok tidak boleh negatif.');
        }

        $media = $type === 'audiobook'
            ? new Audiobook($title, $author, $category, $year, $desc, $stock, max(0, (int) ($in['minutes'] ?? 0)), $id)
            : new Ebook($title, $author, $category, $year, $desc, $stock,
                in_array($in['format'] ?? 'PDF', ['PDF', 'EPUB'], true) ? $in['format'] : 'PDF',
                max(0, (int) ($in['pages'] ?? 0)), $id);

        $this->media->save($media);
        return $media;
    }

    public function delete(int $id): void
    {
        if ($this->loans->activeByMedia($id)) {
            throw new AppException('Media sedang dipinjam dan tidak dapat dihapus.');
        }
        $this->media->delete($id);
    }
}
