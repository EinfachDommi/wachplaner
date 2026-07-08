<?php

namespace Wachplaner\Services\System;

use PDO;
use Throwable;

final class SystemCheckService
{
    public function __construct(private string $rootPath)
    {
    }

    /**
     * @return array<int, array{label:string,value:string,ok:bool,level:string}>
     */
    public function checks(?PDO $pdo = null): array
    {
        $checks = [
            $this->check('Wachplaner Version', (string)\Config::get('version.number') . ' ' . (string)\Config::get('version.codename'), true),
            $this->check('Build', (string)\Config::get('version.build'), true),
            $this->check('Umgebung', (string)\Config::get('app_env'), true),
            $this->check('.env gefunden', \EnvLoader::exists($this->rootPath) ? 'ja' : 'nein', \EnvLoader::exists($this->rootPath)),
            $this->check('Pflicht-Konfiguration', $this->missingConfigLabel(), \Config::missing(['db.host', 'db.name', 'db.user']) === []),
            $this->check('PHP Version', PHP_VERSION, version_compare(PHP_VERSION, '8.1.0', '>=')),
            $this->check('PDO MySQL', extension_loaded('pdo_mysql') ? 'verfügbar' : 'nicht verfügbar', extension_loaded('pdo_mysql')),
            $this->check('Storage beschreibbar', $this->writableLabel('storage'), is_writable($this->rootPath . '/storage')),
            $this->check('Cache beschreibbar', $this->writableLabel('storage/cache'), is_writable($this->rootPath . '/storage/cache')),
            $this->check('Logs beschreibbar', $this->writableLabel('storage/logs'), is_writable($this->rootPath . '/storage/logs')),
        ];

        if ($pdo instanceof PDO) {
            try {
                $pdo->query('SELECT 1');
                $checks[] = $this->check('Datenbankverbindung', 'OK', true);
            } catch (Throwable $e) {
                $checks[] = $this->check('Datenbankverbindung', 'Fehler', false);
            }
        }

        return $checks;
    }

    private function check(string $label, string $value, bool $ok): array
    {
        return ['label' => $label, 'value' => $value, 'ok' => $ok, 'level' => $ok ? 'ok' : 'error'];
    }

    private function missingConfigLabel(): string
    {
        $missing = \Config::missing(['db.host', 'db.name', 'db.user']);
        return $missing === [] ? 'vollständig' : 'fehlt: ' . implode(', ', $missing);
    }

    private function writableLabel(string $relativePath): string
    {
        return is_writable($this->rootPath . '/' . $relativePath) ? 'ja' : 'nein';
    }
}
