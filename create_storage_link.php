<?php
// File untuk create symbolic link storage
// Jalankan sekali, lalu hapus file ini

$target = __DIR__ . '/healthy/storage/app/public';
$link = __DIR__ . '/storage';

// Hapus link lama jika ada
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
    } else {
        echo "Error: /storage sudah ada dan bukan symbolic link!<br>";
        exit;
    }
}

// Buat symbolic link
if (symlink($target, $link)) {
    echo "✅ Symbolic link berhasil dibuat!<br>";
    echo "Target: $target<br>";
    echo "Link: $link<br>";
    echo "<br><strong>Sekarang hapus file ini (create_storage_link.php) untuk keamanan!</strong>";
} else {
    echo "❌ Gagal membuat symbolic link!<br>";
    echo "Kemungkinan server tidak support symbolic link.<br>";
    echo "Gunakan CARA 1: Upload manual folder storage.";
}
?>
