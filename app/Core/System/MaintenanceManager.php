<?php

declare(strict_types=1);

namespace Wachplaner\Core\System;

use PDO;
use Throwable;
use Wachplaner\Services\Logging\Logger;

final class MaintenanceManager
{
    private const DEFAULT_MESSAGE = 'Der Wachplaner wird aktuell gewartet.';
    private const AUTOMATIC_MESSAGE = 'Das System ist vorübergehend nicht erreichbar. Die Verbindung wird automatisch erneut geprüft.';

    private readonly string $stateFile;

    public function __construct(
        private readonly string $rootPath,
        private readonly Logger $logger,
        private readonly int $retrySeconds = 30
    ) {
        $this->stateFile = $rootPath . '/storage/system/maintenance-state.json';
    }

    public function forcedState(): ?MaintenanceState
    {
        if (!(bool) \Config::get('maintenance.force', false)) {
            return null;
        }

        return new MaintenanceState(
            active: true,
            mode: 'forced',
            reason: 'environment_override',
            message: (string) \Config::get('maintenance.message', self::DEFAULT_MESSAGE),
            detectedAt: date(DATE_ATOM),
            lastCheckAt: date(DATE_ATOM)
        );
    }

    public function localState(): MaintenanceState
    {
        if (!is_file($this->stateFile)) {
            return MaintenanceState::online();
        }

        $decoded = json_decode((string) @file_get_contents($this->stateFile), true);

        return is_array($decoded)
            ? MaintenanceState::fromArray($decoded)
            : MaintenanceState::online();
    }

    public function shouldShortCircuit(): bool
    {
        $state = $this->localState();

        if (!$state->isAutomatic() || $state->retryAt === null) {
            return false;
        }

        $retryTimestamp = strtotime($state->retryAt);

        return $retryTimestamp !== false && $retryTimestamp > time();
    }

    public function activateAutomatic(string $reason, ?Throwable $exception = null): MaintenanceState
    {
        $previous = $this->localState();
        $now = date(DATE_ATOM);
        $retryAt = date(DATE_ATOM, time() + $this->retrySeconds);

        $state = new MaintenanceState(
            active: true,
            mode: 'automatic',
            reason: $reason,
            message: self::AUTOMATIC_MESSAGE,
            detectedAt: $previous->isAutomatic() && $previous->detectedAt !== null
                ? $previous->detectedAt
                : $now,
            lastCheckAt: $now,
            retryAt: $retryAt
        );

        $this->writeState($state);

        if (!$previous->isAutomatic()) {
            $this->logger->critical(
                'Automatic maintenance mode activated',
                [
                    'code' => 'SYS-DB-001',
                    'reason' => $reason,
                    'exception' => $exception?->getMessage(),
                    'retry_at' => $retryAt,
                ],
                'system'
            );
        } else {
            $this->logger->warning(
                'Automatic maintenance health check failed',
                [
                    'code' => 'SYS-DB-001',
                    'reason' => $reason,
                    'retry_at' => $retryAt,
                ],
                'system'
            );
        }

        return $state;
    }

    public function recoverAutomatic(): void
    {
        $state = $this->localState();

        if (!$state->isAutomatic()) {
            return;
        }

        $this->clearLocalState();

        $this->logger->info(
            'Database connection restored; automatic maintenance mode deactivated',
            [
                'code' => 'SYS-DB-RECOVERED',
                'detected_at' => $state->detectedAt,
                'recovered_at' => date(DATE_ATOM),
            ],
            'system'
        );
    }

    public function manualState(PDO $pdo): MaintenanceState
    {
        $settings = $this->settings($pdo);
        $active = filter_var(
            $settings['maintenance_enabled'] ?? '0',
            FILTER_VALIDATE_BOOL
        );

        if (!$active) {
            return MaintenanceState::online();
        }

        return new MaintenanceState(
            active: true,
            mode: 'manual',
            reason: 'administrator',
            message: trim((string) ($settings['maintenance_message'] ?? '')) ?: self::DEFAULT_MESSAGE,
            detectedAt: $settings['maintenance_started_at'] ?? null,
            lastCheckAt: date(DATE_ATOM)
        );
    }

    public function registrationEnabled(PDO $pdo): bool
    {
        $settings = $this->settings($pdo);

        return filter_var(
            $settings['registration_enabled'] ?? '1',
            FILTER_VALIDATE_BOOL
        );
    }

    public function updateManual(
        PDO $pdo,
        bool $enabled,
        string $message,
        bool $registrationEnabled,
        int $userId
    ): void {
        $message = trim($message) ?: self::DEFAULT_MESSAGE;
        $startedAt = $enabled ? date(DATE_ATOM) : '';

        $this->saveSetting($pdo, 'maintenance_enabled', $enabled ? '1' : '0', $userId);
        $this->saveSetting($pdo, 'maintenance_message', $message, $userId);
        $this->saveSetting($pdo, 'maintenance_started_at', $startedAt, $userId);
        $this->saveSetting($pdo, 'registration_enabled', $registrationEnabled ? '1' : '0', $userId);

        $this->logger->info(
            $enabled ? 'Manual maintenance mode activated' : 'Manual maintenance mode deactivated',
            [
                'user_id' => $userId,
                'registration_enabled' => $registrationEnabled,
            ],
            'system'
        );
    }

    public function settings(PDO $pdo): array
    {
        $defaults = [
            'maintenance_enabled' => '0',
            'maintenance_message' => self::DEFAULT_MESSAGE,
            'maintenance_started_at' => '',
            'registration_enabled' => '1',
        ];

        try {
            $statement = $pdo->query(
                "SELECT setting_key, setting_value FROM system_settings
                 WHERE setting_key IN (
                    'maintenance_enabled',
                    'maintenance_message',
                    'maintenance_started_at',
                    'registration_enabled'
                 )"
            );

            $settings = $defaults;

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $settings[(string) $row['setting_key']] = (string) ($row['setting_value'] ?? '');
            }

            return $settings;
        } catch (Throwable $exception) {
            $this->logger->warning(
                'System settings are not available; defaults are used',
                [
                    'exception' => $exception->getMessage(),
                    'hint' => 'Run /upgrade to install Guardian migrations.',
                ],
                'system'
            );

            return $defaults;
        }
    }

    private function saveSetting(PDO $pdo, string $key, string $value, int $userId): void
    {
        $statement = $pdo->prepare(
            'INSERT INTO system_settings (setting_key, setting_value, updated_by)
             VALUES (:setting_key, :setting_value, :updated_by)
             ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                updated_by = VALUES(updated_by),
                updated_at = CURRENT_TIMESTAMP'
        );

        $statement->execute([
            'setting_key' => $key,
            'setting_value' => $value,
            'updated_by' => $userId,
        ]);
    }

    private function writeState(MaintenanceState $state): void
    {
        $directory = dirname($this->stateFile);

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        @file_put_contents(
            $this->stateFile,
            json_encode(
                $state->toArray(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
            LOCK_EX
        );
    }

    private function clearLocalState(): void
    {
        if (is_file($this->stateFile)) {
            @unlink($this->stateFile);
        }
    }
}
