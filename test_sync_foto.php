<?php
set_time_limit(300);

echo "<h2>🔧 TEST & SYNC FOTO MANUAL</h2>";
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

// Function untuk sync foto (sama seperti di AdminController)
function syncFotoToPublic($base_dir) {
    $synced_total = 0;
    
    try {
        $source = $base_dir . '/storage/app/public';
        $destination = dirname($base_dir) . '/storage';
        
        // Buat folder jika belum ada
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        // Sync semua folder
        $folders = ['gallery', 'news', 'contents', 'tentang', 'cards'];
        
        foreach ($folders as $folder) {
            $src_folder = $source . '/' . $folder;
            $dest_folder = $destination . '/' . $folder;
            
            if (is_dir($src_folder)) {
                if (!is_dir($dest_folder)) {
                    mkdir($dest_folder, 0755, true);
                }
                
                $files = glob($src_folder . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $filename = basename($file);
                        $dest_file = $dest_folder . '/' . $filename;
                        
                        if (!file_exists($dest_file) || filemtime($file) > filemtime($dest_file)) {
                            if (copy($file, $dest_file)) {
                                $synced_total++;
                            }
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
    return $synced_total;
}

// STEP 1: Cek AdminController.php
echo "<h3>STEP 1: Cek AdminController.php</h3>";

$controller_path = $base_dir . '/app/Http/Controllers/AdminController.php';
if (file_exists($controller_path)) {
    $content = file_get_contents($controller_path);
    
    if (strpos($content, 'syncFotoToPublic') !== false) {
        echo "✅ AdminController.php sudah ada fungsi <code>syncFotoToPublic()</code><br>";
        
        // Cek apakah dipanggil di addGallery
        if (strpos($content, '$this->syncFotoToPublic()') !== false) {
            echo "✅ Fungsi <code>syncFotoToPublic()</code> sudah dipanggil<br>";
        } else {
            echo "❌ Fungsi <code>syncFotoToPublic()</code> BELUM dipanggil di addGallery()<br>";
            echo "⚠️ AdminController.php belum terupdate dengan benar!<br>";
        }
    } else {
        echo "❌ AdminController.php BELUM ada fungsi <code>syncFotoToPublic()</code><br>";
        echo "⚠️ File belum terupload atau ter-overwrite!<br>";
    }
    
    $last_modified = date("Y-m-d H:i:s", filemtime($controller_path));
    echo "📅 Last modified: <strong>$last_modified</strong><br>";
} else {
    echo "❌ AdminController.php tidak ditemukan!<br>";
}

echo "<hr>";

// STEP 2: Jalankan Sync Manual
echo "<h3>STEP 2: Sync Foto Manual</h3>";

$synced = syncFotoToPublic($base_dir);
echo "✅ Foto berhasil di-sync: <strong>$synced file</strong><br>";

echo "<hr>";

// STEP 3: Cek Hasil
echo "<h3>STEP 3: Cek Hasil Sync</h3>";

$folders_to_check = [
    'gallery' => 'Gallery',
    'news' => 'News/Berita',
];

foreach ($folders_to_check as $folder => $label) {
    $dest_folder = dirname($base_dir) . '/storage/' . $folder;
    
    if (is_dir($dest_folder)) {
        $total_files = count(glob($dest_folder . '/*'));
        echo "📁 <strong>$label:</strong> $total_files file<br>";
        
        // Tampilkan 3 file terbaru
        $files = glob($dest_folder . '/*');
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $latest_files = array_slice($files, 0, 3);
        if (count($latest_files) > 0) {
            echo "   File terbaru:<br>";
            foreach ($latest_files as $file) {
                $filename = basename($file);
                $time = date("Y-m-d H:i:s", filemtime($file));
                $url = 'https://naufal.baknus.26.cyberwarrior.co.id/storage/' . $folder . '/' . $filename;
                echo "   • <a href='$url' target='_blank'>$filename</a> ($time)<br>";
            }
        }
        echo "<br>";
    }
}

echo "<hr>";

// STEP 4: Clear Cache Laravel
echo "<h3>STEP 4: Clear Cache Laravel</h3>";

try {
    require $base_dir . '/vendor/autoload.php';
    $app = require_once $base_dir . '/bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    
    Artisan::call('cache:clear');
    echo "✅ Cache cleared<br>";
    
    Artisan::call('config:clear');
    echo "✅ Config cleared<br>";
    
    Artisan::call('view:clear');
    echo "✅ View cleared<br>";
    
} catch (Exception $e) {
    echo "⚠️ Error clear cache: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Ringkasan
echo "<h3>📊 RINGKASAN:</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
echo "<strong>✅ SELESAI!</strong><br><br>";
echo "Total foto yang di-sync: <strong>$synced file</strong><br>";
echo "</div>";

echo "<hr>";

echo "<h3>🎯 LANGKAH SELANJUTNYA:</h3>";
echo "<ol>";
echo "<li>Refresh website dengan <strong>Ctrl + F5</strong></li>";
echo "<li>Cek apakah foto 'GAGA' sudah muncul</li>";
echo "<li>Jika AdminController.php belum terupdate, upload ulang dengan benar</li>";
echo "<li>Test tambah berita baru lagi</li>";
echo "<li><strong style='color: red;'>HAPUS file ini setelah selesai!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ HAPUS FILE INI SETELAH SELESAI!</p>";
?>
