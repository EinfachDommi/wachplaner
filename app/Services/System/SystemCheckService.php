<?php

declare(strict_types=1);

namespace Wachplaner\Services\System;

use PDO;
use Wachplaner\Core\System\Health\CheckResult;
use Wachplaner\Core\System\Health\ConfigCheck;
use Wachplaner\Core\System\Health\DatabaseCheck;
use Wachplaner\Core\System\Health\HealthCheck;
use Wachplaner\Core\System\Health\MigrationCheck;
use Wachplaner\Core\System\Health\PhpExtensionCheck;
use Wachplaner\Core\System\Health\StorageCheck;

final class SystemCheckService
{
    public function __construct(private readonly string $rootPath)
    {
    }

    /**
     * Backward-compatible result for the existing AdminLTE system view.
     *
     * @return list<array{key:string,label:string,value:string,ok:bool,level:string,critical:bool,meta:array<string, scalar|null>}>
     */
    public function checks(?PDO $pdo = null): array
    {
        $staticResults = [
            CheckResult::ok(
                'version',
                'Wachplaner Version',
                trim((string) \Config::get('version.number') . ' ' . (string) \Config::get('version.codename'))
            ),
            CheckResult::ok('build', 'Build', (string) \Config::get('version.build')),
            CheckResult::ok('environment', 'Umgebung', (string) \Config::get('app_env')),
            version_compare(PHP_VERSION, '8.1.0', '>=')
                ? CheckResult::ok('php_version', 'PHP Version', PHP_VERSION, true)
                : CheckResult::error('php_version', 'PHP Version', PHP_VERSION . ' ist zu alt'),
            $this->isHttps()
                ? CheckResult::ok('https', 'HTTPS', 'aktiv', true)
                : CheckResult::warning('https', 'HTTPS', 'nicht erkannt', true),
        ];

        $healthCheck = new HealthCheck([
            new ConfigCheck($this->rootPath),
            new PhpExtensionCheck(),
            new StorageCheck($this->rootPath),
            new DatabaseCheck($pdo),
            new MigrationCheck($pdo, $this->rootPath . '/database/upgrades'),
        ]);

        $results = array_merge($staticResults, $healthCheck->run());

        return array_map(
            static fn (CheckResult $result): array => $result->toArray(),
            $results
        );
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
