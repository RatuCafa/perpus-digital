<?php
declare(strict_types=1);

namespace App\Support;

use App\Models\Media;

/** Membuat sampul buku SVG otomatis dari judul, kategori, dan jenis media. */
final class Cover
{
    private const PALETTE = [
        ['#1d4e89', '#00b2ca'],
        ['#7b2d5f', '#e07a9b'],
        ['#2d6a4f', '#95d5b2'],
        ['#b5541c', '#f4b860'],
        ['#3d348b', '#7678ed'],
        ['#264653', '#e9c46a'],
    ];

    public static function svg(Media $m): string
    {
        [$c1, $c2] = self::PALETTE[crc32($m->getCategory()) % count(self::PALETTE)];
        $gid = 'cg' . $m->getId();

        $lines = explode("\n", wordwrap(trim($m->getTitle()), 12, "\n", false));
        if (count($lines) > 4) {
            $lines = array_slice($lines, 0, 4);
            $lines[3] = self::cut($lines[3], 10) . '...';
        }
        $text = '';
        foreach ($lines as $i => $line) {
            $text .= '<text x="12" y="' . (62 + $i * 15) . '" font-size="12.5" font-weight="700" fill="#fff" font-family="Georgia,serif">'
                . Http::e($line) . '</text>';
        }

        $deco = $m->getType() === 'audiobook'
            ? '<g fill="none" stroke="#fff" stroke-opacity=".35" stroke-width="1.5">'
              . '<circle cx="96" cy="128" r="10"/><circle cx="96" cy="128" r="20"/><circle cx="96" cy="128" r="30"/></g>'
            : '<g stroke="#fff" stroke-opacity=".3" stroke-width="1.5">'
              . '<path d="M70 150 L120 100"/><path d="M85 160 L120 125"/><path d="M100 168 L120 148"/></g>';

        $icon = $m->getType() === 'audiobook'
            ? '<path d="M14 26v-4a8 8 0 0 1 16 0v4" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'
              . '<rect x="12" y="25" width="5" height="9" rx="2" fill="#fff"/><rect x="27" y="25" width="5" height="9" rx="2" fill="#fff"/>'
            : '<path d="M12 30V16h10c4 0 8 1 10 4v14c-2-3-6-4-10-4z" fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>';

        return '<svg class="cover" viewBox="0 0 120 168" role="img" aria-label="Sampul ' . Http::e($m->getTitle()) . '">'
            . '<defs><linearGradient id="' . $gid . '" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0" stop-color="' . $c1 . '"/><stop offset="1" stop-color="' . $c2 . '"/></linearGradient></defs>'
            . '<rect width="120" height="168" rx="4" fill="url(#' . $gid . ')"/>'
            . '<rect width="7" height="168" fill="#000" fill-opacity=".18"/>'
            . $deco . $icon . $text
            . '<text x="12" y="156" font-size="7.5" fill="#fff" fill-opacity=".9" font-family="system-ui,sans-serif">'
            . Http::e(self::cut($m->getAuthor(), 20)) . '</text></svg>';
    }

    private static function cut(string $s, int $max): string
    {
        $chars = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        return count($chars) > $max ? implode('', array_slice($chars, 0, $max)) : $s;
    }
}
