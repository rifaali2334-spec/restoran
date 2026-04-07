<?php
// Upload ke public_html/aditya/ lalu akses via browser

$abiPath = dirname(__DIR__) . '/abi';

echo "<pre>";

// Tampilkan web.php lengkap
echo "=== ISI LENGKAP routes/web.php ===\n\n";
$webphp = file_get_contents($abiPath . '/routes/web.php');
echo htmlspecialchars($webphp);

// Cek via artisan route:list filter galeri
echo "\n\n=== ARTISAN: route:list --name=galeri ===\n";
echo shell_exec("cd $abiPath && php artisan route:list --name=galeri 2>&1");

echo "\n\n=== ARTISAN: route:list --name=admin 2>&1 ===\n";
echo shell_exec("cd $abiPath && php artisan route:list --name=admin 2>&1");

echo "</pre>";
