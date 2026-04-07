<?php
set_time_limit(300);

echo "<h2>🔧 FIX FOTO - TANPA SYMBOLIC LINK</h2>";
echo "<hr>";

// Deteksi path yang benar
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
echo "<hr>";

// STEP 1: Buat folder public/storage/gallery
echo "<h3>STEP 1: Buat Folder Public Storage</h3>";

$public_storage = __DIR__ . '/storage';
$public_gallery = $public_storage . '/gallery';

if (!is_dir($public_storage)) {
    mkdir($public_storage, 0755, true);
    echo "✅ Folder 'storage' dibuat di public<br>";
}

if (!is_dir($public_gallery)) {
    mkdir($public_gallery, 0755, true);
    echo "✅ Folder 'gallery' dibuat di public/storage<br>";
}

echo "<hr>";

// STEP 2: Copy foto dari storage/app/public/gallery ke public/storage/gallery
echo "<h3>STEP 2: Copy Foto dari Storage ke Public</h3>";

$source_storage = $base_dir . '/storage/app/public/gallery';
$dest_public = $public_gallery;

if (is_dir($source_storage)) {
    $files = glob($source_storage . '/*');
    $copied = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $destination = $dest_public . '/' . $filename;
            
            if (copy($file, $destination)) {
                $copied++;
            }
        }
    }
    
    echo "✅ Foto berhasil dicopy: <strong>$copied file</strong><br>";
} else {
    echo "⚠️ Folder storage/app/public/gallery tidak ditemukan<br>";
}

echo "<hr>";

// STEP 3: Copy foto dari images/gallery (jika ada)
echo "<h3>STEP 3: Copy Foto dari Images/Gallery</h3>";

$possible_image_dirs = [
    __DIR__ . '/images/gallery',
    __DIR__ . '/naufal/images/gallery',
    $base_dir . '/public/images/gallery',
];

$found_images = false;
foreach ($possible_image_dirs as $img_dir) {
    if (is_dir($img_dir)) {
        echo "✅ Ditemukan folder: $img_dir<br>";
        $files = glob($img_dir . '/*');
        $copied = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $destination = $dest_public . '/' . $filename;
                
                // Copy ke public/storage/gallery
                if (!file_exists($destination)) {
                    if (copy($file, $destination)) {
                        $copied++;
                    }
                }
                
                // Copy juga ke storage/app/public/gallery
                $dest_storage = $source_storage . '/' . $filename;
                if (!file_exists($dest_storage)) {
                    copy($file, $dest_storage);
                }
            }
        }
        
        echo "✅ Foto berhasil dicopy: <strong>$copied file</strong><br>";
        $found_images = true;
        break;
    }
}

if (!$found_images) {
    echo "⚠️ Folder images/gallery tidak ditemukan<br>";
}

echo "<hr>";

// STEP 4: Update Database
echo "<h3>STEP 4: Update Database</h3>";

try {
    require $base_dir . '/vendor/autoload.php';
    $app = require_once $base_dir . '/bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    
    $galleries = DB::table('galleries')->get();
    $updated = 0;
    
    foreach ($galleries as $gallery) {
        $image_path = $gallery->image;
        
        if (strpos($image_path, 'gallery/') !== 0) {
            $new_path = 'gallery/' . $image_path;
            
            DB::table('galleries')
                ->where('id', $gallery->id)
                ->update(['image' => $new_path]);
            
            $updated++;
        }
    }
    
    echo "✅ Database berhasil diupdate: <strong>$updated record</strong><br>";
    
} catch (Exception $e) {
    echo "❌ Error update database: " . $e->getMessage() . "<br>";
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
    
} catch (Exception $e) {
    echo "⚠️ Error clear cache: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// STEP 6: Cek Hasil
echo "<h3>STEP 6: Cek Hasil</h3>";

$total_files = count(glob($public_gallery . '/*'));
echo "📸 Total foto di public/storage/gallery: <strong>$total_files file</strong><br>";

$sample_files = array_slice(glob($public_gallery . '/*'), 0, 5);
if (count($sample_files) > 0) {
    echo "<br><strong>Sample foto:</strong><br>";
    foreach ($sample_files as $file) {
        $filename = basename($file);
        $url = 'https://naufal.baknus.26.cyberwarrior.co.id/storage/gallery/' . $filename;
        echo "• <a href='$url' target='_blank'>$filename</a><br>";
    }
}

echo "<hr>";

// RINGKASAN
echo "<h3>📊 RINGKASAN:</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
echo "<strong>✅ SELESAI!</strong><br><br>";
echo "Karena server tidak support symbolic link, foto sudah dicopy langsung ke:<br>";
echo "<code>public_html/storage/gallery/</code><br><br>";
echo "Total foto: <strong>$total_files file</strong><br>";
echo "</div>";

echo "<hr>";

echo "<h3>🎯 LANGKAH SELANJUTNYA:</h3>";
echo "<ol>";
echo "<li>Buka website: <a href='https://naufal.baknus.26.cyberwarrior.co.id' target='_blank'>https://naufal.baknus.26.cyberwarrior.co.id</a></li>";
echo "<li>Refresh dengan <strong>Ctrl + F5</strong></li>";
echo "<li>Cek apakah foto sudah muncul</li>";
echo "<li>Test tambah foto baru di admin panel</li>";
echo "<li><strong style='color: red;'>HAPUS file ini setelah selesai!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ CATATAN: Setiap kali admin upload foto baru, foto akan otomatis tersimpan di storage/app/public/gallery/. Tapi karena tidak ada symbolic link, foto tidak akan muncul. Perlu script otomatis untuk copy foto baru ke public/storage/gallery/.</p>";
?>
