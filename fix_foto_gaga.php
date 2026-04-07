<?php
/**
 * Script untuk cek dan copy foto "gaga" yang hilang
 */

echo "<h2>Fix Foto 'GAGA' yang Hilang</h2>";
echo "<hr>";

$fileName = 'VUAcrQEoDAYSV3HkUTMg7DkVMbsCAcpffAIVNqB9.png';

// Lokasi yang mungkin
$locations = [
    'abi_storage' => '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/abi/storage/app/public/news/' . $fileName,
    'naufal_storage' => '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/naufal/storage/news/' . $fileName,
];

echo "<h3>🔍 Cek Lokasi File:</h3>";

$sourceFile = null;
$destinationFile = null;

foreach ($locations as $name => $path) {
    if (file_exists($path)) {
        $fileSize = filesize($path);
        echo "<p style='color: green;'>✅ <strong>$name:</strong> File DITEMUKAN!</p>";
        echo "<p>Path: $path</p>";
        echo "<p>Size: " . number_format($fileSize / 1024, 2) . " KB</p>";
        
        if ($name == 'abi_storage') {
            $sourceFile = $path;
        }
        if ($name == 'naufal_storage') {
            $destinationFile = $path;
        }
    } else {
        echo "<p style='color: red;'>❌ <strong>$name:</strong> File TIDAK ADA</p>";
        echo "<p>Path: $path</p>";
    }
}

echo "<hr>";

// Jika file ada di abi tapi tidak ada di naufal, copy
if ($sourceFile && !$destinationFile) {
    echo "<h3>🔧 Copy File dari ABI ke NAUFAL:</h3>";
    
    $destDir = '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/naufal/storage/news';
    $destFile = $destDir . '/' . $fileName;
    
    // Buat folder jika belum ada
    if (!is_dir($destDir)) {
        if (mkdir($destDir, 0755, true)) {
            echo "<p style='color: green;'>✅ Folder news berhasil dibuat</p>";
        } else {
            echo "<p style='color: red;'>❌ Gagal membuat folder news</p>";
        }
    }
    
    // Copy file
    if (copy($sourceFile, $destFile)) {
        echo "<p style='color: green;'><strong>✅ File berhasil di-copy!</strong></p>";
        echo "<p>From: $sourceFile</p>";
        echo "<p>To: $destFile</p>";
        
        // Verifikasi
        if (file_exists($destFile)) {
            $newSize = filesize($destFile);
            echo "<p style='color: green;'>✅ Verifikasi: File ada di naufal/storage/news/ (" . number_format($newSize / 1024, 2) . " KB)</p>";
            
            echo "<hr>";
            echo "<h3>🎉 SELESAI!</h3>";
            echo "<p><strong>Foto 'GAGA' berhasil di-fix!</strong></p>";
            echo "<p><strong>Test URL:</strong></p>";
            echo "<p><a href='https://naufal.baknus.26.cyberwarrior.co.id/storage/news/$fileName' target='_blank' style='color: blue; font-size: 18px;'>https://naufal.baknus.26.cyberwarrior.co.id/storage/news/$fileName</a></p>";
            echo "<p>Klik link di atas untuk test foto muncul atau tidak</p>";
            
        } else {
            echo "<p style='color: red;'>❌ Error: File tidak ditemukan setelah copy</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Gagal copy file</p>";
    }
    
} elseif (!$sourceFile && !$destinationFile) {
    echo "<h3 style='color: red;'>❌ MASALAH:</h3>";
    echo "<p><strong>File foto tidak ditemukan di ABI maupun NAUFAL!</strong></p>";
    echo "<p><strong>SOLUSI:</strong></p>";
    echo "<ol>";
    echo "<li>Login ke admin panel</li>";
    echo "<li>Edit berita 'gaga' (ID 10)</li>";
    echo "<li>Upload ulang fotonya</li>";
    echo "<li>Save</li>";
    echo "</ol>";
    
} elseif ($destinationFile) {
    echo "<h3 style='color: green;'>✅ File sudah ada di NAUFAL!</h3>";
    echo "<p><strong>Tapi foto tidak muncul di website?</strong></p>";
    echo "<p><strong>Kemungkinan masalah:</strong></p>";
    echo "<ul>";
    echo "<li>Cache browser (tekan Ctrl+F5 untuk refresh)</li>";
    echo "<li>Path di view salah</li>";
    echo "<li>File corrupt</li>";
    echo "</ul>";
    echo "<p><strong>Test URL:</strong></p>";
    echo "<p><a href='https://naufal.baknus.26.cyberwarrior.co.id/storage/news/$fileName' target='_blank' style='color: blue; font-size: 18px;'>https://naufal.baknus.26.cyberwarrior.co.id/storage/news/$fileName</a></p>";
}

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p style='color: red;'><strong>⚠️ HAPUS file ini setelah selesai!</strong></p>";
