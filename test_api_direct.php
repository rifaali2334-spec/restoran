<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;
use App\Models\Gallery;
use App\Models\ContactMessage;
use App\Models\Content;

echo "=== Testing Database Connection ===\n\n";

$stats = [
    'total_news' => News::where('is_published', true)->count(),
    'total_galleries' => Gallery::count(),
    'total_messages' => ContactMessage::count(),
    'total_contents' => Content::count(),
];

echo "Stats:\n";
echo json_encode($stats, JSON_PRETTY_PRINT);
echo "\n\n";

echo "=== Testing API Response Format ===\n\n";

$response = [
    'success' => true,
    'data' => [
        'stats' => $stats,
        'chart_data' => []
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
echo "\n";
