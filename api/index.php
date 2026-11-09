<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

// Siapkan folder penyimpanan sementara di /tmp (karena Vercel read-only)
$storagePath = '/tmp/storage';
foreach ([
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Siapkan database SQLite sementara di /tmp
$dbFile = '/tmp/database.sqlite';
$isNewDb = !file_exists($dbFile);
if ($isNewDb) {
    touch($dbFile);
}

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Arahkan storage Laravel ke /tmp
$app->useStoragePath($storagePath);

// Jalankan migrasi dan seeder otomatis jika database baru dibuat
if ($isNewDb) {
    try {
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
    } catch (\Throwable $e) {
        // Abaikan jika sudah termigrasi
    }
}

$app->handleRequest(Request::capture());