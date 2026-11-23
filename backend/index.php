<?php

require_once __DIR__ . '/src/Models/Media.php';
require_once __DIR__ . '/src/Models/Anime.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Services/Library.php';

use App\Models\Media;
use App\Models\Anime;
use App\Models\User;
use App\Services\Library;

function input(string $message): string
{
    echo $message;
    return trim((string) fgets(STDIN));
}

$library = new Library();
$user = new User("Nindi");

$initialMedia = [
    new Anime("Naruto", 2002, "Studio Pierrot"),
    new Anime("One Piece", 1999, "Toei Animation"),
    new Anime("Demon Slayer", 2019, "Ufotable"),
    new Anime("Jujutsu Kaisen", 2020, "MAPPA"),
    new Anime("Attack on Titan", 2013, "MAPPA"),
    new Media("Spirited Away", "Movie", 2001),
    new Media("Your Name", "Movie", 2016),
    new Anime("Haikyuu", 2014, "Production I.G"),
    new Anime("Death Note", 2006, "Madhouse"),
    new Media("A Silent Voice", "Movie", 2016)
];

foreach ($initialMedia as $media) {
    $library->addMedia($media);
}


$user->addToCollection($initialMedia[0]);
$user->addToCollection($initialMedia[5]);
$user->addToCollection($initialMedia[7]);

// Program utama
while (true) {

    echo "\n";
    echo "========================================\n";
    echo "          FLIPPLAY BACKEND SYSTEM\n";
    echo "========================================\n";
    echo "1. Menambahkan Media ke Library FlipPlay\n";
    echo "2. Daftar Media Tersedia\n";
    echo "3. Pencarian Media\n";
    echo "4. Detail Media\n";
    echo "5. Koleksi Pengguna\n";
    echo "6. Keluar\n";
    echo "========================================\n";

    $choice = input("Masukkan pilihan (1-6): ");

    // MENU 1: Menambahkan media
    if ($choice === "1") {

        echo "\n===== TAMBAHKAN MEDIA =====\n";

        $title = input("Masukkan judul media : ");

        if ($title === "") {
            echo "Judul tidak boleh kosong!\n";
            continue;
        }

        $type = input("Masukkan jenis media (Anime/Movie): ");

        if ($type === "") {
            echo "Jenis media tidak boleh kosong!\n";
            continue;
        }

        $yearInput = input("Masukkan tahun media : ");

        if (!ctype_digit($yearInput) ||
            (int) $yearInput < 1888 ||
            (int) $yearInput > (int) date("Y")) {
            echo "Tahun tidak valid!\n";
            continue;
        }

        $year = (int) $yearInput;

        if (strtolower($type) === "anime") {

            $studio = input("Masukkan nama studio : ");

            $newMedia = new Anime($title, $year, $studio);

        } else {

            $newMedia = new Media($title, $type, $year);

        }

        if ($library->addMedia($newMedia)) {
            echo "\nMedia berhasil ditambahkan ke Library FlipPlay!\n";
        } else {
            echo "\nMedia dengan judul tersebut sudah tersedia!\n";
        }

    }

    // MENU 2: Menampilkan daftar media
    elseif ($choice === "2") {

        echo "\n===== DAFTAR MEDIA FLIPPLAY =====\n";

        foreach ($library->getAllMedia() as $index => $media) {

            echo ($index + 1) . ". "
                . $media->getTitle()
                . " (" . $media->getType() . ")"
                . " - " . $media->getYear()
                . "\n";
        }

        echo "\nTotal media: "
            . count($library->getAllMedia())
            . "\n";

    }

    // MENU 3: Pencarian media
    elseif ($choice === "3") {

        echo "\n===== PENCARIAN MEDIA =====\n";

        $keyword = input("Masukkan judul media yang dicari: ");

        $result = $library->findMedia($keyword);

        if ($result !== null) {
            echo "\nMedia ditemukan!\n";
            echo $result->getInfo() . "\n";
        } else {
            echo "\nMedia tidak ditemukan!\n";
        }

    }

    // MENU 4: Detail media
    elseif ($choice === "4") {

        echo "\n===== DETAIL MEDIA =====\n";

        $title = input("Masukkan judul media: ");

        $result = $library->findMedia($title);

        if ($result !== null) {
            echo "\n";
            echo $result->getInfo() . "\n";
        } else {
            echo "\nMedia tidak ditemukan!\n";
        }

    }

    // MENU 5: Koleksi pengguna
    elseif ($choice === "5") {

        echo "\n===== KOLEKSI PENGGUNA =====\n";
        echo "Nama Pengguna: " . $user->getName() . "\n";

        echo "\n1. Lihat koleksi\n";
        echo "2. Tambahkan media ke koleksi\n";

        $collectionChoice = input("Pilih menu (1-2): ");

        if ($collectionChoice === "1") {

            $collection = $user->getCollection();

            if (count($collection) === 0) {
                echo "\nKoleksi masih kosong.\n";
            } else {
                foreach ($collection as $index => $media) {
                    echo ($index + 1) . ". "
                        . $media->getTitle()
                        . " (" . $media->getYear() . ")\n";
                }
            }

        } elseif ($collectionChoice === "2") {

            echo "\n===== DAFTAR MEDIA =====\n";

            foreach ($library->getAllMedia() as $index => $media) {
                echo ($index + 1) . ". "
                    . $media->getTitle() . "\n";
            }

            $numberInput = input("Masukkan nomor media: ");

            if (!ctype_digit($numberInput) || (int) $numberInput < 1) {
                echo "Nomor tidak valid!\n";
                continue;
            }

            $selectedMedia = $library->getMediaByNumber((int) $numberInput);

            if ($selectedMedia !== null) {
                $oldCount = count($user->getCollection());

                $user->addToCollection($selectedMedia);

                if (count($user->getCollection()) > $oldCount) {
                    echo "\nMedia berhasil ditambahkan ke koleksi!\n";
                } else {
                    echo "\nMedia sudah ada di koleksi!\n";
                }

            } else {
                echo "\nMedia tidak ditemukan!\n";
            }

        } else {
            echo "\nPilihan tidak tersedia!\n";
        }

    }

    // MENU 6: Keluar
    elseif ($choice === "6") {

        echo "\nTerima kasih telah menggunakan FlipPlay!\n";
        break;

    }

    // Pilihan tidak valid
    else {

        echo "\nPilihan tidak tersedia! Silakan pilih 1-6.\n";

    }
}
