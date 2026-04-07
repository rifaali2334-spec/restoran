<?php
set_time_limit(300); // 5 menit timeout

echo "<h2>🔧 FIX FOTO OTOMATIS</h2>";
echo "<hr>";

$base_dir = __DIR__ . '/naufal';
$errors = [];
$success = [];

// STEP 1: Rename folder storage yang salah
echo "<h3>STEP 1: Rename Folder Storage</h3>";
$old_storage = $base_dir . '/storage';
$backup_storage = $base_dir . '/storage_backup';

if (file_exists($old_storage) && !is_link($old_storage)) {
    if (rename($old_storage, $backup_storage)) {
        echo "✅ Folder 'storage' berhasil direname jadi 'storage_backup'<br>";
        $success[] = "Rename storage folder";
    } else {
        echo "❌ Gagal rename folder 'storage'<br>";
        $errors[] = "Rename storage folder";
    }
} else {
    echo "ℹ️ Folder 'storage' tidak ada atau sudah symbolic link<br>";
}

echo "<hr>";

// STEP 2: Buat Symbolic Link
echo "<h3>STEP 2: Buat Symbolic Link</h3>";
$link = $base_dir . '/storage';
$target = $base_dir . '/healthy/storage/app/public';

if (!file_exists($link)) {
    if (symlink($target, $link)) {
        echo "✅ Symbolic link berhasil dibuat!<br>";
        echo "📍 Link: $link<br>";
        echo "🎯 Target: $target<br>";
        $success[] = "Create symbolic link";
    } else {
        echo "❌ Gagal membuat symbolic link!<br>";
        echo "⚠️ Server mungkin tidak support symlink. Hubungi hosting support.<br>";
        $errors[] = "Create symbolic link";
    }
} else {
    if (is_link($link)) {
        echo "✅ Symbolic link sudah ada!<br>";
        $success[] = "Symbolic link exists";
    } else {
        echo "❌ Folder 'storage' masih ada! Hapus manual dulu.<br>";
        $errors[] = "Storage folder still exists";
    }
}

echo "<hr>";

// STEP 3: Pindahkan Foto Lama ke Storage
echo "<h3>STEP 3: Pindahkan Foto ke Storage</h3>";
$source_dir = $base_dir . '/images/gallery';
$dest_dir = $base_dir . '/healthy/storage/app/public/gallery';

// Buat folder gallery jika belum ada
if (!is_dir($dest_dir)) {
    mkdir($dest_dir, 0775, true);
    echo "✅ Folder gallery dibuat di storage<br>";
}

if (is_dir($source_dir)) {
    $files = glob($source_dir . '/*');
    $copied = 0;
    $failed = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $destination = $dest_dir . '/' . $filename;
            
            if (!file_exists($destination)) {
                if (copy($file, $destination)) {
                    $copied++;
                } else {
                    $failed++;
                }
            }
        }
    }
    
    echo "✅ Foto berhasil dipindahkan: <strong>$copied file</strong><br>";
    if ($failed > 0) {
        echo "⚠️ Foto gagal dipindahkan: <strong>$failed file</strong><br>";
    }
    $success[] = "Copy $copied photos";
} else {
    echo "⚠️ Folder sumber foto tidak ditemukan<br>";
}

echo "<hr>";

// STEP 4: Update Database
echo "<h3>STEP 4: Update Database</h3>";

try {
    require $base_dir . '/healthy/vendor/autoload.php';
    $app = require_once $base_dir . '/healthy/bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    
    // Update path gallery di database
    $galleries = DB::table('galleries')->get();
    $updated = 0;
    
    foreach ($galleries as $gallery) {
        $image_path = $gallery->image;
        
        // Jika belum format baru (belum ada 'gallery/')
        if (strpos($image_path, 'gallery/') !== 0) {
            $new_path = 'gallery/' . $image_path;
            
            DB::table('galleries')
                ->where('id', $gallery->id)
                ->update(['image' => $new_path]);
            
            $updated++;
        }
    }
    
    echo "✅ Database berhasil diupdate: <strong>$updated record</strong><br>";
    $success[] = "Update $updated database records";
    
} catch (Exception $e) {
    echo "❌ Error update database: " . $e->getMessage() . "<br>";
    $errors[] = "Update database: " . $e->getMessage();
}

echo "<hr>";

// STEP 5: Clear Cache
echo "<h3>STEP 5: Clear Cache</h3>";

try {
    Artisan::call('cache:clear');
    echo "✅ Cache cleared<br>";
    
    Artisan::call('config:clear');
    echo "✅ Config cleared<br>";
    
    Artisan::call('view:clear');
    echo "✅ View cleared<br>";
    
    $success[] = "Clear cache";
    
} catch (Exception $e) {
    echo "⚠️ Error clear cache: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// STEP 6: Set Permission
echo "<h3>STEP 6: Set Permission</h3>";

$storage_path = $base_dir . '/healthy/storage';
$cache_path = $base_dir . '/healthy/bootstrap/cache';

function chmod_recursive($path, $filemode) {
    if (!is_dir($path)) return false;
    
    $dh = opendir($path);
    while (($file = readdir($dh)) !== false) {
        if ($file != '.' && $file != '..') {
            $fullpath = $path . '/' . $file;
            if (is_link($fullpath)) continue;
            if (!is_dir($fullpath)) {
                if (!chmod($fullpath, $filemode)) {
                    return false;
                }
            } else {
                if (!chmod($fullpath, $filemode)) {
                    return false;
                }
                if (!chmod_recursive($fullpath, $filemode)) {
                    return false;
                }
            }
        }
    }
    closedir($dh);
    return chmod($path, $filemode);
}

if (chmod_recursive($storage_path, 0775)) {
    echo "✅ Permission storage folder: 775<br>";
    $success[] = "Set storage permission";
} else {
    echo "⚠️ Gagal set permission storage<br>";
}

if (chmod_recursive($cache_path, 0775)) {
    echo "✅ Permission cache folder: 775<br>";
    $success[] = "Set cache permission";
} else {
    echo "⚠️ Gagal set permission cache<br>";
}

echo "<hr>";

// RINGKASAN
echo "<h3>📊 RINGKASAN:</h3>";

echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
echo "<strong>✅ BERHASIL (" . count($success) . "):</strong><br>";
foreach ($success as $item) {
    echo "• $item<br>";
}
echo "</div>";

if (count($errors) > 0) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
    echo "<strong>❌ GAGAL (" . count($errors) . "):</strong><br>";
    foreach ($errors as $item) {
        echo "• $item<br>";
    }
    echo "</div>";
}

echo "<hr>";

// LANGKAH SELANJUTNYA
echo "<h3>🎯 LANGKAH SELANJUTNYA:</h3>";
echo "<ol>";
echo "<li>Buka website: <a href='https://naufal.baknus.26.cyberwarrior.co.id' target='_blank'>https://naufal.baknus.26.cyberwarrior.co.id</a></li>";
echo "<li>Refresh dengan <strong>Ctrl + F5</strong></li>";
echo "<li>Cek apakah foto sudah muncul</li>";
echo "<li>Test tambah foto baru di admin panel</li>";
echo "<li><strong style='color: red;'>HAPUS file ini (fix_foto_otomatis.php) setelah selesai!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ PENTING: HAPUS FILE INI SETELAH SELESAI!</p>";
?>
