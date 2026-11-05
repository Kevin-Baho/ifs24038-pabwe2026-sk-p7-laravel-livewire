<?php

declare(strict_types=1);

namespace Illuminate\Foundation {
    if (! function_exists('Illuminate\Foundation\is_writable')) {
        function is_writable(string $filename): bool
        {
            if (is_dir($filename)) {
                $test = rtrim($filename, '\\/').DIRECTORY_SEPARATOR.'compat_test_'.uniqid('', true).'.tmp';
                $handle = @fopen($test, 'wb');
                if ($handle === false) {
                    return false;
                }
                fclose($handle);
                @unlink($test);
                return true;
            }

            if (file_exists($filename)) {
                $handle = @fopen($filename, 'ab');
                if ($handle !== false) {
                    fclose($handle);
                    return true;
                }
            }

            return \is_writable($filename);
        }
    }
}

namespace PHPUnit\TextUI\Configuration {
    if (! function_exists('PHPUnit\TextUI\Configuration\is_readable')) {
        function is_readable(string $filename): bool
        {
            return file_exists($filename);
        }
    }
}
