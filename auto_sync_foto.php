<?php
// Auto-sync foto dari storage ke public
// Jalankan via cron job atau akses manual

$base_dir = __DIR__ . '/healthy';
$source = $base_dir . '/storage/app/public';
$destination = __DIR__ . '/storage';

$synced = 0;
$folders = ['gallery', 'news', 'contents', 'tentang', 'cards'];

foreach ($folders as $folder) {
    $src_folder = $source . '/' . $folder;
    $dest_folder = $destination . '/' . $folder;
    
    if (!is_dir($dest_folder)) {
        mkdir($dest_folder, 0755, true);
    }
    
    if (is_dir($src_folder)) {
        $files = glob($src_folder . '/*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $dest_file = $dest_folder . '/' . $filename;
                
                if (!file_exists($dest_file) || filemtime($file) > filemtime($dest_file)) {
                    if (copy($file, $dest_file)) {
                        $synced++;
                    }
                }
            }
        }
    }
}

// Return JSON untuk monitoring
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'synced' => $synced,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
