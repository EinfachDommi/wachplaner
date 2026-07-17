<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Health;

use PDO;
use Throwable;
use Wachplaner\Core\System\Contracts\SystemCheckInterface;

final class MigrationCheck implements SystemCheckInterface
{
    public function __construct(
        private readonly ?PDO $pdo,
        private readonly string $upgradePath
    ) {
    }

    public function key(): string
    {
        return 'migrations';
    }

    public function run(): CheckResult
    {
        if (!$this->pdo instanceof PDO) {
            return CheckResult::error(
                $this->key(),
                'Migrationen',
                'nicht prüfbar'
            );
        }

        try {
            $files = glob(rtrim($this->upgradePath, '/') . '/*.sql') ?: [];
            $available = array_map('basename', $files);
            sort($available);

            $tableExists = (bool) $this->pdo->query(
                "SELECT COUNT(*) FROM information_schema.tables
                 WHERE table_schema = DATABASE()
                   AND table_name = 'system_migrations'"
            )->fetchColumn();

            if (!$tableExists) {
                return CheckResult::warning(
                    $this->key(),
                    'Migrationen',
                    'Migrationstabelle fehlt – /upgrade ausführen',
                    true
                );
            }

            $executed = $this->pdo
                ->query('SELECT migration FROM system_migrations')
                ->fetchAll(PDO::FETCH_COLUMN) ?: [];

            $pending = array_values(array_diff($available, $executed));

            if ($pending !== []) {
                return CheckResult::warning(
                    $this->key(),
                    'Migrationen',
                    sprintf('%d ausstehend', count($pending)),
                    true,
                    ['pending_count' => count($pending)]
                );
            }

            return CheckResult::ok(
                $this->key(),
                'Migrationen',
                sprintf('%d aktuell', count($available)),
                true
            );
        } catch (Throwable) {
            return CheckResult::error(
                $this->key(),
                'Migrationen',
                'Prüfung fehlgeschlagen'
            );
        }
    }
}
