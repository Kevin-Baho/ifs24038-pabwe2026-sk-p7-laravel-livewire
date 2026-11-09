<?php

// Tampilkan semua error
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    // 1. Siapkan folder writable di /tmp
    $storage = '/tmp/storage';
    foreach ([
        $storage . '/framework/views',
        $storage . '/framework/cache/data',
        $storage . '/framework/sessions',
        $storage . '/logs',
    ] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    // 2. Setup SQLite di /tmp
    $dbFile = '/tmp/database.sqlite';
    $needMigration = !file_exists($dbFile) || filesize($dbFile) === 0;
    if (!file_exists($dbFile)) {
        touch($dbFile);
    }

    // 3. Set environment variable yang dibutuhkan
    putenv("VIEW_COMPILED_PATH={$storage}/framework/views");
    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$dbFile}");
    putenv("CACHE_STORE=array");
    putenv("SESSION_DRIVER=cookie");

    $_ENV['VIEW_COMPILED_PATH'] = "{$storage}/framework/views";
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $dbFile;
    $_ENV['CACHE_STORE'] = 'array';
    $_ENV['SESSION_DRIVER'] = 'cookie';

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath($storage);

    // 4. Jalankan migrasi & seeding otomatis pada database baru
    if ($needMigration) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $seederErr) {
            // Abaikan jika sudah pernah di-seed
        }
    }

    // 5. Tangani request
    $app->handleRequest(\Illuminate\Http\Request::capture());

} catch (\Throwable $e) {
    // Ubah status ke 200 agar Vercel tidak menutup layar dengan gambar rubah
    http_response_code(200);
    echo "<div style='font-family: sans-serif; padding: 2rem; background: #0f172a; color: #f87171;'>";
    echo "<h2 style='color: #ef4444;'>Detail Error di Vercel:</h2>";
    echo "<p><strong>Pesan:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Baris: " . $e->getLine() . ")</p>";
    echo "<pre style='background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 8px; overflow-x: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}