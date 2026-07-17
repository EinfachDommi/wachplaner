<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Health;

use PDO;
use Throwable;
use Wachplaner\Core\System\Contracts\SystemCheckInterface;

final class DatabaseCheck implements SystemCheckInterface
{
    public function __construct(private readonly ?PDO $pdo)
    {
    }

    public function key(): string
    {
        return 'database';
    }

    public function run(): CheckResult
    {
        if (!$this->pdo instanceof PDO) {
            return CheckResult::error(
                $this->key(),
                'Datenbankverbindung',
                'nicht verfügbar'
            );
        }

        try {
            $startedAt = microtime(true);
            $healthy = (int) $this->pdo->query('SELECT 1')->fetchColumn() === 1;
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            if (!$healthy) {
                return CheckResult::error(
                    $this->key(),
                    'Datenbankverbindung',
                    'Gesundheitsprüfung fehlgeschlagen'
                );
            }

            return CheckResult::ok(
                $this->key(),
                'Datenbankverbindung',
                sprintf('OK (%d ms)', $durationMs),
                true,
                ['duration_ms' => $durationMs]
            );
        } catch (Throwable) {
            return CheckResult::error(
                $this->key(),
                'Datenbankverbindung',
                'nicht erreichbar'
            );
        }
    }
}
