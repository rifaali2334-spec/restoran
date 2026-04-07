<?php
/**
 * Script FIX KHUSUS untuk hosting dengan struktur berbeda
 * 
 * Masalah:
 * - Folder public/ tidak ada di /naufal/
 * - Foto ada di storage/gallery/ (bukan storage/app/public/gallery/)
 * 
 * Solusi:
 * - Buat struktur folder yang benar
 * - Pindahkan foto ke lokasi yang benar
 * - Buat symbolic link
 */

set_time_limit(300);

echo "<h2>Fix Struktur Folder - Hosting Khusus</h2>";
echo "<hr>";

$baseDir = __DIR__;
echo "<p><strong>Base Directory:</strong> $baseDir</p>";
echo "<hr>";

// STEP 1: Buat struktur folder yang benar
echo "<h3>STEP 1: Buat Struktur Folder</h3>";

$folders = [
    'storage/app',
    'storage/app/public',
    'storage/app/public/gallery',
    'storage/app/public/news',
    'storage/app/public/contents',
    'storage/app/public/tentang',
    'storage/app/public/cards'
];

foreach ($folders as $folder) {
    $fullPath = $baseDir . '/' . $folder;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "<p style='color: green;'>✅ Folder <strong>$folder/</strong> berhasil dibuat</p>";
        } else {
            echo "<p style='color: red;'>❌ Gagal membuat folder <strong>$folder/</strong></p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Folder <strong>$folder/</strong> sudah ada</p>";
    }
}

echo "<hr>";

// STEP 2: Pindahkan foto dari storage/gallery ke storage/app/public/gallery
echo "<h3>STEP 2: Pindahkan Foto Gallery</h3>";

$oldGalleryPath = $baseDir . '/storage/gallery';
$newGalleryPath = $baseDir . '/storage/app/public/gallery';

if (is_dir($oldGalleryPath)) {
    $files = scandir($oldGalleryPath);
    $movedCount = 0;
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.gitignore') {
            $oldFile = $oldGalleryPath . '/' . $file;
            $newFile = $newGalleryPath . '/' . $file;
            
            if (is_file($oldFile)) {
                if (copy($oldFile, $newFile)) {
                    $movedCount++;
                }
            }
        }
    }
    
    echo "<p style='color: green;'>✅ <strong>$movedCount</strong> foto gallery berhasil dipindahkan</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Folder storage/gallery tidak ditemukan</p>";
}

echo "<hr>";

// STEP 3: Pindahkan foto lainnya (news, contents, tentang, cards)
echo "<h3>STEP 3: Pindahkan Foto Lainnya</h3>";

$otherFolders = ['news', 'contents', 'tentang', 'cards'];

foreach ($otherFolders as $folderName) {
    $oldPath = $baseDir . '/storage/' . $folderName;
    $newPath = $baseDir . '/storage/app/public/' . $folderName;
    
    if (is_dir($oldPath)) {
        $files = scandir($oldPath);
        $movedCount = 0;
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != '.gitignore') {
                $oldFile = $oldPath . '/' . $file;
                $newFile = $newPath . '/' . $file;
                
                if (is_file($oldFile)) {
                    if (copy($oldFile, $newFile)) {
                        $movedCount++;
                    }
                }
            }
        }
        
        echo "<p style='color: green;'>✅ <strong>$movedCount</strong> foto $folderName berhasil dipindahkan</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Folder storage/$folderName tidak ditemukan (skip)</p>";
    }
}

echo "<hr>";

// STEP 4: Cari folder public yang sebenarnya
echo "<h3>STEP 4: Cari Folder Public</h3>";

$possiblePublicPaths = [
    $baseDir . '/public',
    dirname($baseDir),
    '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html'
];

$publicPath = null;
foreach ($possiblePublicPaths as $path) {
    if (is_dir($path)) {
        // Cek apakah ini folder public yang benar (ada index.php atau naufal folder)
        if (file_exists($path . '/index.php') || is_dir($path . '/naufal')) {
            $publicPath = $path;
            echo "<p style='color: green;'>✅ Folder public ditemukan: <strong>$path</strong></p>";
            break;
        }
    }
}

if (!$publicPath) {
    echo "<p style='color: red;'>❌ Folder public tidak ditemukan</p>";
} else {
    // STEP 5: Buat symbolic link atau copy file
    echo "<hr>";
    echo "<h3>STEP 5: Buat Akses ke Storage</h3>";
    
    $publicStoragePath = $publicPath . '/storage';
    $storageAppPublicPath = $baseDir . '/storage/app/public';
    
    // Hapus jika sudah ada
    if (file_exists($publicStoragePath)) {
        if (is_link($publicStoragePath)) {
            unlink($publicStoragePath);
            echo "<p>🗑️ Symbolic link lama dihapus</p>";
        }
    }
    
    // Coba buat symbolic link
    if (!file_exists($publicStoragePath)) {
        $relativePath = 'naufal/storage/app/public';
        
        if (@symlink($relativePath, $publicStoragePath)) {
            echo "<p style='color: green;'>✅ Symbolic link berhasil dibuat!</p>";
            echo "<p>Link: $publicStoragePath → $relativePath</p>";
        } else {
            // Jika symlink gagal, copy file
            echo "<p style='color: orange;'>⚠️ Symbolic link gagal, menggunakan copy file</p>";
            
            if (!is_dir($publicStoragePath)) {
                mkdir($publicStoragePath, 0755, true);
            }
            
            // Copy semua folder
            $copyFolders = ['gallery', 'news', 'contents', 'tentang', 'cards'];
            $totalCopied = 0;
            
            foreach ($copyFolders as $folder) {
                $srcFolder = $storageAppPublicPath . '/' . $folder;
                $dstFolder = $publicStoragePath . '/' . $folder;
                
                if (is_dir($srcFolder)) {
                    if (!is_dir($dstFolder)) {
                        mkdir($dstFolder, 0755, true);
                    }
                    
                    $files = scandir($srcFolder);
                    $count = 0;
                    foreach ($files as $file) {
                        if ($file != '.' && $file != '..' && $file != '.gitignore') {
                            if (copy($srcFolder . '/' . $file, $dstFolder . '/' . $file)) {
                                $count++;
                            }
                        }
                    }
                    
                    $totalCopied += $count;
                    echo "<p>✅ Copy $folder: $count files</p>";
                }
            }
            
            echo "<p style='color: green;'>✅ Total <strong>$totalCopied</strong> file berhasil di-copy</p>";
        }
    }
}

echo "<hr>";

// STEP 6: Verifikasi
echo "<h3>STEP 6: Verifikasi</h3>";

$verifyFolders = [
    'storage/app/public/gallery',
    'storage/app/public/news',
    'storage/app/public/contents',
    'storage/app/public/tentang',
    'storage/app/public/cards'
];

echo "<p><strong>Struktur folder baru:</strong></p>";
echo "<ul>";
foreach ($verifyFolders as $folder) {
    $fullPath = $baseDir . '/' . $folder;
    if (is_dir($fullPath)) {
        $fileCount = count(scandir($fullPath)) - 2;
        echo "<li style='color: green;'>✅ $folder ($fileCount files)</li>";
    } else {
        echo "<li style='color: red;'>❌ $folder (tidak ada)</li>";
    }
}
echo "</ul>";

if ($publicPath) {
    $publicStoragePath = $publicPath . '/storage';
    if (file_exists($publicStoragePath)) {
        echo "<p style='color: green;'>✅ Akses public/storage sudah tersedia</p>";
        
        if (is_link($publicStoragePath)) {
            echo "<p>Tipe: Symbolic Link</p>";
        } else {
            echo "<p>Tipe: Folder Copy</p>";
        }
    }
}

echo "<hr>";

// Kesimpulan
echo "<h3>🎉 SELESAI!</h3>";
echo "<p><strong>Yang sudah dilakukan:</strong></p>";
echo "<ol>";
echo "<li>✅ Struktur folder storage/app/public/ dibuat</li>";
echo "<li>✅ Foto dipindahkan ke lokasi yang benar</li>";
echo "<li>✅ Akses public/storage dibuat (symlink atau copy)</li>";
echo "</ol>";

echo "<p style='color: red;'><strong>⚠️ PENTING:</strong></p>";
echo "<ol>";
echo "<li><strong>HAPUS file ini (fix_struktur_hosting.php) untuk keamanan!</strong></li>";
echo "<li>Upload file <strong>app/Models/Gallery.php</strong> yang sudah diperbaiki</li>";
echo "<li>Clear cache: php artisan cache:clear</li>";
echo "<li>Test website, cek apakah foto muncul</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
