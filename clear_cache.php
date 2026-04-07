<?php
// File untuk clear cache Laravel
// Jalankan sekali, lalu hapus file ini

require __DIR__ . '/healthy/vendor/autoload.php';
$app = require_once __DIR__ . '/healthy/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>Clearing Laravel Cache...</h2>";

// Clear config cache
$kernel->call('config:clear');
echo "✅ Config cache cleared<br>";

// Clear view cache
$kernel->call('view:clear');
echo "✅ View cache cleared<br>";

// Clear route cache
$kernel->call('route:clear');
echo "✅ Route cache cleared<br>";

echo "<br><strong>Cache berhasil di-clear! Sekarang hapus file ini (clear_cache.php)</strong>";
?>
