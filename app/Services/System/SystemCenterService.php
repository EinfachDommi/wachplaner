<?php

declare(strict_types=1);

namespace Wachplaner\Services\System;

use PDO;
use Wachplaner\Core\Settings\FeatureFlagService;
use Wachplaner\Core\Settings\SettingsService;
use Wachplaner\Core\System\MaintenanceManager;

final class SystemCenterService
{
    public function __construct(
        private readonly string $rootPath,
        private readonly PDO $pdo,
        private readonly MaintenanceManager $maintenance,
        private readonly SettingsService $settings,
        private readonly FeatureFlagService $featureFlags
    ) {
    }

    /** @return array<string, mixed> */
    public function data(): array
    {
        $systemCheck = new SystemCheckService($this->rootPath);
        $checks = $systemCheck->checks($this->pdo);

        return [
            'checks' => $checks,
            'healthSummary' => $this->healthSummary($checks),
            'counts' => $this->counts(),
            'maintenanceSettings' => $this->maintenance->settings($this->pdo),
            'localMaintenanceState' => $this->maintenance->localState(),
            'systemSettings' => $this->settings->system(),
            'featureFlags' => $this->featureFlags->all(),
            'logs' => $this->recentLogs(),
            'logFiles' => $this->logFiles(),
        ];
    }

    /** @param list<array<string, mixed>> $checks */
    private function healthSummary(array $checks): array
    {
        $critical = 0;
        $warnings = 0;

        foreach ($checks as $check) {
            if (($check['ok'] ?? false) === true) {
                continue;
            }
            if (($check['critical'] ?? false) === true || ($check['level'] ?? '') === 'error') {
                $critical++;
            } else {
                $warnings++;
            }
        }

        return [
            'status' => $critical > 0 ? 'critical' : ($warnings > 0 ? 'warning' : 'healthy'),
            'critical' => $critical,
            'warnings' => $warnings,
            'total' => count($checks),
        ];
    }

    /** @return array<string, int> */
    private function counts(): array
    {
        $tables = [
            'Fahrzeugtypen' => 'vehicle_types',
            'Ausbildungen' => 'training_types',
            'Erweiterungen' => 'expansions',
            'Baukosten' => 'station_build_costs',
            'Projekte' => 'projects',
        ];
        $counts = [];

        foreach ($tables as $label => $table) {
            $counts[$label] = (int) $this->pdo
                ->query(sprintf('SELECT COUNT(*) FROM `%s`', $table))
                ->fetchColumn();
        }

        return $counts;
    }

    /** @return list<array{channel:string,line:string}> */
    private function recentLogs(int $perFile = 8): array
    {
        $result = [];

        foreach ($this->logFiles() as $file) {
            $lines = @file($file['path'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach (array_slice($lines, -$perFile) as $line) {
                $result[] = ['channel' => $file['channel'], 'line' => (string) $line];
            }
        }

        return array_slice(array_reverse($result), 0, 30);
    }

    /** @return list<array{channel:string,path:string,size:int,modified:int}> */
    private function logFiles(): array
    {
        $directory = $this->rootPath . '/storage/logs';
        $files = [];

        foreach (glob($directory . '/*.log') ?: [] as $path) {
            $files[] = [
                'channel' => pathinfo($path, PATHINFO_FILENAME),
                'path' => $path,
                'size' => (int) (filesize($path) ?: 0),
                'modified' => (int) (filemtime($path) ?: 0),
            ];
        }

        usort($files, static fn (array $a, array $b): int => $b['modified'] <=> $a['modified']);

        return $files;
    }
}
