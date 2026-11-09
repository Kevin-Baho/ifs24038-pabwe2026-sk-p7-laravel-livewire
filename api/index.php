<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

// 1. Siapkan folder writable di /tmp (karena filesystem Vercel read-only)
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

// 2. Siapkan database SQLite sementara di /tmp
$dbFile = '/tmp/database.sqlite';
$isNewDb = !file_exists($dbFile) || filesize($dbFile) === 0;
if (!file_exists($dbFile)) {
    touch($dbFile);
}

// 3. Set environment variable darurat untuk Vercel
putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$dbFile}");
putenv("SESSION_DRIVER=cookie");
putenv("CACHE_STORE=array");

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Arahkan storage Laravel ke folder /tmp
$app->useStoragePath($storagePath);

// 4. Jalankan migrasi & seeding otomatis pada database baru
if ($isNewDb) {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
    } catch (\Throwable $e) {
        // Lanjutkan jika ada peringatan seeder
    }
}

// 5. Tangani request
$app->handleRequest(Request::capture());