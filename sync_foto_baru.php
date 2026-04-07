<?php
set_time_limit(300);

echo "<h2>🔄 SYNC FOTO BARU</h2>";
echo "<hr>";

// Deteksi base directory
$possible_paths = [
    __DIR__ . '/healthy',
    __DIR__ . '/naufal/healthy',
];

$base_dir = null;
foreach ($possible_paths as $path) {
    if (is_dir($path)) {
        $base_dir = $path;
        break;
    }
}

if (!$base_dir) {
    die("❌ Folder 'healthy' tidak ditemukan!");
}

echo "✅ Base directory: <strong>$base_dir</strong><br><br>";

// Folder yang akan di-sync
$folders_to_sync = [
    'gallery' => 'Gallery',
    'news' => 'News/Berita',
    'contents' => 'Content',
    'cards' => 'Cards',
    'tentang' => 'Tentang'
];

$total_synced = 0;

foreach ($folders_to_sync as $folder => $label) {
    echo "<h3>📁 Sync $label:</h3>";
    
    $source = $base_dir . '/storage/app/public/' . $folder;
    $destination = __DIR__ . '/storage/' . $folder;
    
    // Buat folder destination jika belum ada
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
        echo "✅ Folder destination dibuat<br>";
    }
    
    if (is_dir($source)) {
        $files = glob($source . '/*');
        $synced = 0;
        $skipped = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $dest_file = $destination . '/' . $filename;
                
                // Copy jika belum ada atau file lebih baru
                if (!file_exists($dest_file)) {
                    if (copy($file, $dest_file)) {
                        $synced++;
                        $total_synced++;
                    }
                } else {
                    // Update jika file source lebih baru
                    if (filemtime($file) > filemtime($dest_file)) {
                        if (copy($file, $dest_file)) {
                            $synced++;
                            $total_synced++;
                        }
                    } else {
                        $skipped++;
                    }
                }
            }
        }
        
        echo "✅ Synced: <strong>$synced file</strong><br>";
        if ($skipped > 0) {
            echo "ℹ️ Skipped (sudah ada): <strong>$skipped file</strong><br>";
        }
        
        // Tampilkan total file di destination
        $total_files = count(glob($destination . '/*'));
        echo "📊 Total file di public: <strong>$total_files file</strong><br>";
        
    } else {
        echo "⚠️ Folder source tidak ditemukan: $source<br>";
    }
    
    echo "<br>";
}

echo "<hr>";

// Ringkasan
echo "<h3>📊 RINGKASAN:</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
echo "<strong>✅ SELESAI!</strong><br><br>";
echo "Total foto yang di-sync: <strong>$total_synced file</strong><br>";
echo "</div>";

echo "<hr>";

// Cek sample foto
echo "<h3>🔍 CEK SAMPLE FOTO:</h3>";

$sample_folders = ['gallery', 'news'];
foreach ($sample_folders as $folder) {
    $dest_folder = __DIR__ . '/storage/' . $folder;
    if (is_dir($dest_folder)) {
        $files = array_slice(glob($dest_folder . '/*'), 0, 3);
        if (count($files) > 0) {
            echo "<strong>$folder:</strong><br>";
            foreach ($files as $file) {
                $filename = basename($file);
                $url = 'https://naufal.baknus.26.cyberwarrior.co.id/storage/' . $folder . '/' . $filename;
                echo "• <a href='$url' target='_blank'>$filename</a><br>";
            }
            echo "<br>";
        }
    }
}

echo "<hr>";

echo "<h3>🎯 LANGKAH SELANJUTNYA:</h3>";
echo "<ol>";
echo "<li>Refresh website dengan <strong>Ctrl + F5</strong></li>";
echo "<li>Cek apakah foto sudah muncul semua</li>";
echo "<li>Upload <code>AdminController.php</code> yang baru supaya foto baru otomatis sync</li>";
echo "<li><strong style='color: red;'>HAPUS file ini setelah selesai!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ HAPUS FILE INI SETELAH SELESAI!</p>";
?>
