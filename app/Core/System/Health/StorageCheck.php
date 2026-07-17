<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Health;

use Throwable;
use Wachplaner\Core\System\Contracts\SystemCheckInterface;

final class StorageCheck implements SystemCheckInterface
{
    public function __construct(private readonly string $rootPath)
    {
    }

    public function key(): string
    {
        return 'storage';
    }

    public function run(): CheckResult
    {
        $directories = [
            'logs' => $this->rootPath . '/storage/logs',
            'cache' => $this->rootPath . '/storage/cache',
            'system' => $this->rootPath . '/storage/system',
            'uploads' => $this->rootPath . '/storage/uploads',
        ];

        $failed = [];

        foreach ($directories as $name => $directory) {
            if (!$this->testDirectory($directory)) {
                $failed[] = $name;
            }
        }

        if ($failed !== []) {
            return CheckResult::error(
                $this->key(),
                'Storage Schreibtest',
                'nicht schreibbar: ' . implode(', ', $failed)
            );
        }

        return CheckResult::ok(
            $this->key(),
            'Storage Schreibtest',
            'alle Verzeichnisse schreibbar',
            true
        );
    }

    private function testDirectory(string $directory): bool
    {
        if (!is_dir($directory) && !@mkdir($directory, 0775, true)) {
            return false;
        }

        try {
            $testFile = $directory . '/.guardian-' . bin2hex(random_bytes(5));

            if (@file_put_contents($testFile, 'guardian', LOCK_EX) === false) {
                return false;
            }

            return @file_get_contents($testFile) === 'guardian';
        } catch (Throwable) {
            return false;
        } finally {
            if (isset($testFile) && is_file($testFile)) {
                @unlink($testFile);
            }
        }
    }
}
