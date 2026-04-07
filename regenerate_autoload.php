<?php
// Script untuk regenerate autoload di server
// Jalankan sekali, lalu hapus file ini

require __DIR__ . '/healthy/vendor/autoload.php';

echo "<h2>Regenerating Autoload...</h2>";

// Load composer
$composerAutoload = __DIR__ . '/healthy/vendor/composer/autoload_files.php';

if (file_exists($composerAutoload)) {
    // Tambahkan helpers.php ke autoload
    $helpersPath = __DIR__ . '/healthy/app/helpers.php';
    
    if (file_exists($helpersPath)) {
        require_once $helpersPath;
        echo "✅ helpers.php loaded successfully<br>";
        echo "✅ Function storage_asset() is now available<br>";
    } else {
        echo "❌ helpers.php not found!<br>";
    }
} else {
    echo "❌ Composer autoload not found!<br>";
}

echo "<br><strong>Sekarang refresh website dan test lagi!</strong><br>";
echo "<strong>Jangan lupa hapus file ini (regenerate_autoload.php)</strong>";
?>
