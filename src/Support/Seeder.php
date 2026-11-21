<?php
declare(strict_types=1);

namespace App\Support;

use App\Models\{Admin, Audiobook, Ebook, User};
use App\Repositories\{MediaRepository, UserRepository};

/** Mengisi data awal (admin + beberapa media) saat berkas JSON masih kosong. */
final class Seeder
{
    public static function run(UserRepository $users, MediaRepository $media): void
    {
        if ($users->all() === []) {
            $users->save(new Admin('Administrator', 'admin@pustaka.test', User::hashPassword('admin123')));
        }
        if ($media->all() === []) {
            $media->save(new Ebook('Laskar Pelangi', 'Andrea Hirata', 'Novel', 2005, 'Kisah sepuluh anak Belitung yang berjuang untuk bersekolah.', 3, 'EPUB', 529));
            $media->save(new Ebook('Pemrograman PHP Modern', 'Tim Informatika', 'Teknologi', 2023, 'Dasar OOP, namespace, dan struktur proyek PHP 8.', 5, 'PDF', 320));
            $media->save(new Ebook('Algoritma dan Struktur Data', 'R. Munir', 'Teknologi', 2016, 'Pengantar algoritma dengan contoh kode.', 2, 'PDF', 410));
            $media->save(new Audiobook('Filosofi Teras', 'Henry Manampiring', 'Pengembangan Diri', 2018, 'Stoisisme untuk kehidupan sehari-hari.', 2, 360));
            $media->save(new Audiobook('Bumi Manusia', 'Pramoedya Ananta Toer', 'Novel', 1980, 'Awal tetralogi Buru, dibacakan lengkap.', 1, 720));
        }
    }
}
