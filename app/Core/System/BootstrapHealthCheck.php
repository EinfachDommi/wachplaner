<?php

declare(strict_types=1);

namespace Wachplaner\Core\System;

use PDO;
use Throwable;

final class BootstrapHealthCheck
{
    public function database(PDO $pdo): bool
    {
        try {
            return (int) $pdo->query('SELECT 1')->fetchColumn() === 1;
        } catch (Throwable) {
            return false;
        }
    }

    public function storage(string $rootPath): bool
    {
        $directory = $rootPath . '/storage/system';

        if (!is_dir($directory) && !@mkdir($directory, 0775, true)) {
            return false;
        }

        $testFile = $directory . '/.health-' . bin2hex(random_bytes(5));

        try {
            if (@file_put_contents($testFile, 'ok', LOCK_EX) === false) {
                return false;
            }

            return @file_get_contents($testFile) === 'ok';
        } finally {
            if (is_file($testFile)) {
                @unlink($testFile);
            }
        }
    }
}
