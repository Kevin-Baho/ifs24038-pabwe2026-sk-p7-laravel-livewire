<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    // 1. Alihkan Log ke stderr agar tidak menulis ke file log yang read-only
    putenv("LOG_CHANNEL=stderr");
    $_ENV['LOG_CHANNEL'] = 'stderr';

    // 2. Folder compiled views di /tmp
    $viewPath = '/tmp/views';
    if (!is_dir($viewPath)) {
        mkdir($viewPath, 0755, true);
    }
    putenv("VIEW_COMPILED_PATH={$viewPath}");
    $_ENV['VIEW_COMPILED_PATH'] = $viewPath;

    // 3. Setup SQLite di /tmp
    $dbFile = '/tmp/database.sqlite';
    $needMigration = !file_exists($dbFile) || filesize($dbFile) === 0;
    if (!file_exists($dbFile)) {
        touch($dbFile);
    }
    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$dbFile}");
    putenv("CACHE_STORE=array");
    putenv("SESSION_DRIVER=cookie");

    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $dbFile;
    $_ENV['CACHE_STORE'] = 'array';
    $_ENV['SESSION_DRIVER'] = 'cookie';

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 4. Jalankan migrasi & seeding otomatis pada database baru
    if ($needMigration) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $mErr) {
            // Abaikan jika sudah termigrasi
        }
    }

    $app->handleRequest(\Illuminate\Http\Request::capture());

} catch (\Throwable $e) {
    http_response_code(200);
    echo "<div style='font-family: sans-serif; padding: 2rem; background: #0f172a; color: #f87171;'>";
    echo "<h2 style='color: #ef4444;'>Detail Error di Vercel:</h2>";
    echo "<p><strong>Pesan:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Baris: " . $e->getLine() . ")</p>";
    echo "<pre style='background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 8px; overflow-x: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}