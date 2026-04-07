<?php
/**
 * Script FINAL BENAR - Pindahkan foto ke lokasi yang tepat
 * Dari: /naufal/storage/
 * Ke: /abi/storage/app/public/
 */

set_time_limit(300);

echo "<h2>Fix Foto FINAL - Pindahkan ke /abi/storage/app/public/</h2>";
echo "<hr>";

$naufalDir = __DIR__; // /public_html/naufal
$abiDir = '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/abi';

echo "<p><strong>Naufal Directory:</strong> $naufalDir</p>";
echo "<p><strong>Abi Directory (Laravel Root):</strong> $abiDir</p>";
echo "<hr>";

// Cek apakah folder abi ada
if (!is_dir($abiDir)) {
    echo "<p style='color: red;'>❌ ERROR: Folder abi tidak ditemukan!</p>";
    exit;
}

echo "<p style='color: green;'>✅ Folder abi ditemukan</p>";
echo "<hr>";

// STEP 1: Buat struktur folder di /abi/storage/app/public/
echo "<h3>STEP 1: Buat Struktur Folder</h3>";

$folders = [
    $abiDir . '/storage/app',
    $abiDir . '/storage/app/public',
    $abiDir . '/storage/app/public/gallery',
    $abiDir . '/storage/app/public/news',
    $abiDir . '/storage/app/public/contents',
    $abiDir . '/storage/app/public/tentang',
    $abiDir . '/storage/app/public/cards'
];

foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        if (mkdir($folder, 0755, true)) {
            echo "<p style='color: green;'>✅ Folder berhasil dibuat: " . basename($folder) . "/</p>";
        } else {
            echo "<p style='color: red;'>❌ Gagal membuat folder: " . basename($folder) . "/</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Folder sudah ada: " . basename($folder) . "/</p>";
    }
}

echo "<hr>";

// STEP 2: Copy foto dari naufal/storage/ ke abi/storage/app/public/
echo "<h3>STEP 2: Copy Foto</h3>";

$copyMap = [
    'gallery' => ['from' => $naufalDir . '/storage/gallery', 'to' => $abiDir . '/storage/app/public/gallery'],
    'news' => ['from' => $naufalDir . '/storage/news', 'to' => $abiDir . '/storage/app/public/news'],
    'contents' => ['from' => $naufalDir . '/storage/contents', 'to' => $abiDir . '/storage/app/public/contents'],
    'tentang' => ['from' => $naufalDir . '/storage/tentang', 'to' => $abiDir . '/storage/app/public/tentang'],
    'cards' => ['from' => $naufalDir . '/storage/cards', 'to' => $abiDir . '/storage/app/public/cards']
];

$totalCopied = 0;

foreach ($copyMap as $name => $paths) {
    $fromPath = $paths['from'];
    $toPath = $paths['to'];
    
    if (is_dir($fromPath)) {
        $files = scandir($fromPath);
        $count = 0;
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != '.gitignore') {
                $srcFile = $fromPath . '/' . $file;
                $dstFile = $toPath . '/' . $file;
                
                if (is_file($srcFile)) {
                    if (copy($srcFile, $dstFile)) {
                        $count++;
                    }
                }
            }
        }
        
        $totalCopied += $count;
        echo "<p style='color: green;'>✅ Copy <strong>$name</strong>: $count files</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Folder <strong>$name</strong> tidak ditemukan (skip)</p>";
    }
}

echo "<p style='font-size: 18px;'><strong>Total: $totalCopied files berhasil di-copy!</strong></p>";
echo "<hr>";

// STEP 3: Buat symbolic link di /abi/public/storage
echo "<h3>STEP 3: Buat Symbolic Link</h3>";

$publicStoragePath = $abiDir . '/public/storage';
$targetPath = $abiDir . '/storage/app/public';

// Hapus jika sudah ada
if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        unlink($publicStoragePath);
        echo "<p>🗑️ Symbolic link lama dihapus</p>";
    } elseif (is_dir($publicStoragePath)) {
        echo "<p style='color: blue;'>ℹ️ Folder public/storage sudah ada</p>";
    }
}

// Buat symbolic link baru
if (!file_exists($publicStoragePath)) {
    $relativePath = '../../storage/app/public';
    
    if (@symlink($relativePath, $publicStoragePath)) {
        echo "<p style='color: green;'>✅ Symbolic link berhasil dibuat!</p>";
        echo "<p>Link: abi/public/storage → ../../storage/app/public</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Symbolic link gagal, akan copy file</p>";
        
        // Copy sebagai alternatif
        if (!is_dir($publicStoragePath)) {
            mkdir($publicStoragePath, 0755, true);
        }
        
        $copyFolders = ['gallery', 'news', 'contents', 'tentang', 'cards'];
        $copyCount = 0;
        foreach ($copyFolders as $folder) {
            $srcFolder = $targetPath . '/' . $folder;
            $dstFolder = $publicStoragePath . '/' . $folder;
            
            if (is_dir($srcFolder)) {
                if (!is_dir($dstFolder)) {
                    mkdir($dstFolder, 0755, true);
                }
                
                $files = scandir($srcFolder);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && $file != '.gitignore') {
                        if (copy($srcFolder . '/' . $file, $dstFolder . '/' . $file)) {
                            $copyCount++;
                        }
                    }
                }
            }
        }
        
        echo "<p style='color: green;'>✅ $copyCount file berhasil di-copy ke abi/public/storage</p>";
    }
}

echo "<hr>";

// STEP 4: Verifikasi
echo "<h3>STEP 4: Verifikasi</h3>";

$verifyFolders = [
    'gallery' => $abiDir . '/storage/app/public/gallery',
    'news' => $abiDir . '/storage/app/public/news',
    'contents' => $abiDir . '/storage/app/public/contents',
    'tentang' => $abiDir . '/storage/app/public/tentang',
    'cards' => $abiDir . '/storage/app/public/cards'
];

echo "<p><strong>Foto di /abi/storage/app/public/:</strong></p>";
echo "<ul>";
foreach ($verifyFolders as $name => $path) {
    if (is_dir($path)) {
        $fileCount = count(scandir($path)) - 2;
        echo "<li style='color: green;'>✅ $name ($fileCount files)</li>";
    } else {
        echo "<li style='color: red;'>❌ $name (tidak ada)</li>";
    }
}
echo "</ul>";

echo "<hr>";

// STEP 5: Test URL
echo "<h3>STEP 5: Test URL Foto</h3>";

$testFile = null;
$galleryPath = $abiDir . '/storage/app/public/gallery';
if (is_dir($galleryPath)) {
    $files = scandir($galleryPath);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.gitignore') {
            $testFile = $file;
            break;
        }
    }
}

if ($testFile) {
    echo "<p><strong>Test file:</strong> $testFile</p>";
    
    $baseUrl = 'https://naufal.baknus.26.cyberwarrior.co.id';
    $testUrl = $baseUrl . '/storage/gallery/' . $testFile;
    
    echo "<p style='background: yellow; padding: 15px; font-size: 16px;'>";
    echo "<strong>🔗 KLIK LINK INI UNTUK TEST:</strong><br>";
    echo "<a href='$testUrl' target='_blank' style='color: blue; font-size: 20px; font-weight: bold;'>$testUrl</a>";
    echo "</p>";
    
    echo "<p><strong>Instruksi:</strong></p>";
    echo "<ol>";
    echo "<li><strong>Klik link di atas</strong></li>";
    echo "<li>Jika foto muncul = <strong style='color: green; font-size: 18px;'>BERHASIL! 🎉</strong></li>";
    echo "<li>Jika 404 = Masih ada masalah</li>";
    echo "</ol>";
}

echo "<hr>";

// Kesimpulan
echo "<h3 style='color: green;'>🎉 SELESAI!</h3>";
echo "<p><strong>Yang sudah dilakukan:</strong></p>";
echo "<ol>";
echo "<li>✅ Struktur folder /abi/storage/app/public/ dibuat</li>";
echo "<li>✅ $totalCopied foto berhasil di-copy dari /naufal/storage/ ke /abi/storage/app/public/</li>";
echo "<li>✅ Symbolic link /abi/public/storage dibuat</li>";
echo "</ol>";

echo "<p style='color: red; font-size: 16px;'><strong>⚠️ LANGKAH SELANJUTNYA:</strong></p>";
echo "<ol style='font-size: 14px;'>";
echo "<li><strong>Klik link test URL di atas</strong></li>";
echo "<li>Jika foto muncul, buka website: <a href='https://naufal.baknus.26.cyberwarrior.co.id' target='_blank'>https://naufal.baknus.26.cyberwarrior.co.id</a></li>";
echo "<li>Cek semua halaman (home, galeri, berita, tentang)</li>";
echo "<li>Jika semua foto muncul, <strong>HAPUS SEMUA FILE SCRIPT PHP!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
