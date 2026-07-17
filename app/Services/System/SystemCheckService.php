<?php

declare(strict_types=1);

namespace Wachplaner\Services\System;

use PDO;
use Throwable;

final class SystemCheckService
{
    public function __construct(private readonly string $rootPath)
    {
    }

    /**
     * @return list<array{label:string,value:string,ok:bool,level:string}>
     */
    public function checks(?PDO $pdo = null): array
    {
        $checks = [
            $this->check(
                'Wachplaner Version',
                (string) \Config::get('version.number') . ' ' . (string) \Config::get('version.codename'),
                true
            ),
            $this->check('Build', (string) \Config::get('version.build'), true),
            $this->check('Umgebung', (string) \Config::get('app_env'), true),
            $this->check(
                '.env gefunden',
                \EnvLoader::exists($this->rootPath) ? 'ja' : 'nein',
                \EnvLoader::exists($this->rootPath)
            ),
            $this->check(
                'Pflicht-Konfiguration',
                $this->missingConfigLabel(),
                \Config::missing(['db.host', 'db.name', 'db.user']) === []
            ),
            $this->check(
                'PHP Version',
                PHP_VERSION,
                version_compare(PHP_VERSION, '8.1.0', '>=')
            ),
            $this->extensionCheck('PDO MySQL', 'pdo_mysql'),
            $this->extensionCheck('DOM/XML', 'dom'),
            $this->extensionCheck('SimpleXML', 'simplexml'),
            $this->extensionCheck('ZIP', 'zip'),
            $this->extensionCheck('mbstring', 'mbstring'),
            $this->extensionCheck('OpenSSL', 'openssl'),
            $this->storageCheck(),
            $this->check(
                'HTTPS',
                $this->isHttps() ? 'aktiv' : 'nicht erkannt',
                $this->isHttps()
            ),
        ];

        if ($pdo instanceof PDO) {
            try {
                $ok = (int) $pdo->query('SELECT 1')->fetchColumn() === 1;
                $checks[] = $this->check(
                    'Datenbankverbindung',
                    $ok ? 'OK' : 'Fehler',
                    $ok
                );
            } catch (Throwable) {
                $checks[] = $this->check('Datenbankverbindung', 'Fehler', false);
            }
        }

        return $checks;
    }

    private function check(string $label, string $value, bool $ok): array
    {
        return [
            'label' => $label,
            'value' => $value,
            'ok' => $ok,
            'level' => $ok ? 'ok' : 'error',
        ];
    }

    private function storageCheck(): array
    {
        $ok = $this->storageWriteTest();

        return $this->check(
            'Storage Schreibtest',
            $ok ? 'erfolgreich' : 'fehlgeschlagen',
            $ok
        );
    }

    private function extensionCheck(string $label, string $extension): array
    {
        $loaded = extension_loaded($extension);

        return $this->check(
            $label,
            $loaded ? 'verfügbar' : 'nicht verfügbar',
            $loaded
        );
    }

    private function missingConfigLabel(): string
    {
        $missing = \Config::missing(['db.host', 'db.name', 'db.user']);

        return $missing === []
            ? 'vollständig'
            : 'fehlt: ' . implode(', ', $missing);
    }

    private function storageWriteTest(): bool
    {
        $directory = $this->rootPath . '/storage/system';

        if (!is_dir($directory) && !@mkdir($directory, 0775, true)) {
            return false;
        }

        $testFile = $directory . '/.system-check-' . bin2hex(random_bytes(4));

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

    private function isHttps(): bool
    {
        return (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
            || str_starts_with((string) \Config::get('app_url', ''), 'https://')
        );
    }
}
