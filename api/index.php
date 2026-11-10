<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    // 1. Alihkan Log ke stderr agar tidak menulis ke file log yang read-only
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL']    = 'stderr';
    $_SERVER['LOG_CHANNEL'] = 'stderr';

    // 2. Folder compiled views di /tmp (satu-satunya direktori writable di Vercel)
    $viewPath = '/tmp/views';
    if (!is_dir($viewPath)) {
        mkdir($viewPath, 0755, true);
    }
    putenv("VIEW_COMPILED_PATH={$viewPath}");
    $_ENV['VIEW_COMPILED_PATH']    = $viewPath;
    $_SERVER['VIEW_COMPILED_PATH'] = $viewPath;

    // 3. Bootstrap cache di /tmp (bootstrap/cache read-only di Vercel)
    $bootstrapCache = '/tmp/bootstrap/cache';
    if (!is_dir($bootstrapCache)) {
        mkdir($bootstrapCache, 0755, true);
    }

    // Salin packages.php yang sudah di-commit ke /tmp agar package discovery tidak perlu ditulis ulang
    $srcPackages = __DIR__ . '/../bootstrap/cache/packages.php';
    $dstPackages = $bootstrapCache . '/packages.php';
    if (file_exists($srcPackages) && !file_exists($dstPackages)) {
        copy($srcPackages, $dstPackages);
    }

    // 4. Setup Storage di /tmp/storage agar upload file dan temporary data writable
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath)) {
        mkdir($storagePath . '/framework/views', 0755, true);
        mkdir($storagePath . '/framework/sessions', 0755, true);
        mkdir($storagePath . '/framework/cache', 0755, true);
        mkdir($storagePath . '/logs', 0755, true);
        mkdir($storagePath . '/app/public', 0755, true);
    }

    // 5. Setup SQLite di /tmp
    $dbFile        = '/tmp/database.sqlite';
    $needMigration = !file_exists($dbFile) || filesize($dbFile) === 0;
    if (!file_exists($dbFile)) {
        touch($dbFile);
    }
    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$dbFile}");
    putenv('CACHE_STORE=array');
    putenv('SESSION_DRIVER=cookie');

    $_ENV['DB_CONNECTION']  = 'sqlite';
    $_ENV['DB_DATABASE']    = $dbFile;
    $_ENV['CACHE_STORE']    = 'array';
    $_ENV['SESSION_DRIVER'] = 'cookie';

    $_SERVER['DB_CONNECTION']  = 'sqlite';
    $_SERVER['DB_DATABASE']    = $dbFile;
    $_SERVER['CACHE_STORE']    = 'array';
    $_SERVER['SESSION_DRIVER'] = 'cookie';

    // Fallback APP_KEY jika belum diset di environment variables
    if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
        $appKey = 'base64:P9L3SY2/90CnF3q2roWDYSJ62MRbwa4LAe8AUd/MHdc=';
        putenv("APP_KEY={$appKey}");
        $_ENV['APP_KEY']    = $appKey;
        $_SERVER['APP_KEY'] = $appKey;
    }

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 6. Redirect bootstrap path dan storage path ke /tmp
    $app->useBootstrapPath('/tmp/bootstrap');
    $app->useStoragePath($storagePath);

    // 7. Tangkap request dan daftarkan ke container SEBELUM kernel/bootstrap apapun,
    //    supaya UrlGenerator mendapat instance Request yang valid saat boot.
    $request = \Illuminate\Http\Request::capture();
    $app->instance('request', $request);

    // 8. Boot kernel — semua service provider terdaftar di sini
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    // 9. Jalankan migrasi & seeding otomatis pada database baru
    if ($needMigration) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            \Illuminate\Support\Facades\Artisan::call('db:seed',  ['--force' => true]);
        } catch (\Throwable $mErr) {
            // Abaikan jika sudah termigrasi
        }
    }

    // 10. Handle request & kirim response
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<div style='font-family: sans-serif; padding: 2rem; background: #0f172a; color: #f87171;'>";
    echo "<h2 style='color: #ef4444;'>Detail Error di Vercel:</h2>";
    echo "<p><strong>Pesan:</strong> "  . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> "   . htmlspecialchars($e->getFile()) . " (Baris: " . $e->getLine() . ")</p>";
    echo "<pre style='background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 8px; overflow-x: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
