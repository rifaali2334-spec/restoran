<?php
// Upload ke public_html/aditya/ akses via browser

echo "<pre>";

$publicHtml = dirname(__DIR__); // public_html

echo "=== ISI public_html/ ===\n";
foreach (scandir($publicHtml) as $item) {
    if (in_array($item, ['.', '..'])) continue;
    $path = $publicHtml . '/' . $item;
    $type = is_dir($path) ? '📁' : '📄';
    echo "$type $item\n";
}

echo "\n=== CEK LARAVEL DI TIAP FOLDER ===\n";
foreach (scandir($publicHtml) as $item) {
    if (in_array($item, ['.', '..', 'aditya'])) continue;
    $path = $publicHtml . '/' . $item;
    if (is_dir($path)) {
        $hasArtisan = file_exists($path . '/artisan');
        $hasEnv     = file_exists($path . '/.env');
        if ($hasArtisan || $hasEnv) {
            echo "✅ $item — Laravel project (artisan=" . ($hasArtisan?'YA':'TIDAK') . ", .env=" . ($hasEnv?'YA':'TIDAK') . ")\n";
            // Baca DB name dari .env
            if ($hasEnv) {
                $env = file_get_contents($path . '/.env');
                preg_match('/DB_DATABASE=(.+)/', $env, $m);
                echo "   DB: " . trim($m[1] ?? '?') . "\n";
            }
        }
    }
}

echo "</pre>";
