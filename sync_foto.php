<?php
// Script untuk auto-sync foto dari storage ke public
// Jalankan via cron job setiap 5 menit atau panggil setelah upload

$base_dir = __DIR__ . '/healthy';
$source = $base_dir . '/storage/app/public/gallery';
$destination = __DIR__ . '/storage/gallery';

// Buat folder jika belum ada
if (!is_dir($destination)) {
    mkdir($destination, 0755, true);
}

// Sync foto
$synced = 0;
if (is_dir($source)) {
    $files = glob($source . '/*');
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $dest_file = $destination . '/' . $filename;
            
            // Copy jika belum ada atau file lebih baru
            if (!file_exists($dest_file) || filemtime($file) > filemtime($dest_file)) {
                if (copy($file, $dest_file)) {
                    $synced++;
                }
            }
        }
    }
}

// Return JSON untuk API call
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'synced' => $synced,
    'message' => "$synced foto berhasil disync"
]);
?>
