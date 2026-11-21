<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\{Audiobook, Ebook, Entity};

class MediaRepository extends JsonRepository
{
    protected function hydrate(array $r): Entity
    {
        $id = (int) $r['id'];
        if (($r['type'] ?? 'ebook') === 'audiobook') {
            return new Audiobook($r['title'], $r['author'], $r['category'], (int) $r['year'],
                $r['description'], (int) $r['stock'], (int) ($r['minutes'] ?? 0), $id);
        }
        return new Ebook($r['title'], $r['author'], $r['category'], (int) $r['year'],
            $r['description'], (int) $r['stock'], $r['format'] ?? 'PDF', (int) ($r['pages'] ?? 0), $id);
    }
}
