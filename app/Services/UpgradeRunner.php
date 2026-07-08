<?php

namespace Wachplaner\Services;

use PDO;
use Throwable;

class UpgradeRunner
{
    private PDO $pdo;
    private string $upgradePath;

    public function __construct(PDO $pdo, string $upgradePath)
    {
        $this->pdo = $pdo;
        $this->upgradePath = rtrim($upgradePath, '/');
    }

    public function ensureMigrationTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS system_migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(190) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            duration_ms INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function pending(): array
    {
        $this->ensureMigrationTable();
        $files = glob($this->upgradePath . '/*.sql') ?: [];
        sort($files);
        $executed = $this->pdo->query('SELECT migration FROM system_migrations')->fetchAll(PDO::FETCH_COLUMN) ?: [];
        $executed = array_flip($executed);

        return array_values(array_filter(array_map('basename', $files), static function (string $file) use ($executed): bool {
            return !isset($executed[$file]);
        }));
    }

    public function run(): array
    {
        $this->ensureMigrationTable();
        $results = [];

        foreach ($this->pending() as $migration) {
            $file = $this->upgradePath . '/' . $migration;
            $sql = file_get_contents($file);
            $start = microtime(true);

            try {
                $this->pdo->beginTransaction();
                $this->pdo->exec($sql);
                $duration = (int)round((microtime(true) - $start) * 1000);
                $stmt = $this->pdo->prepare('INSERT INTO system_migrations (migration, duration_ms) VALUES (?, ?)');
                $stmt->execute([$migration, $duration]);
                $this->pdo->commit();
                $results[] = ['migration' => $migration, 'status' => 'success', 'duration_ms' => $duration];
            } catch (Throwable $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                $results[] = ['migration' => $migration, 'status' => 'error', 'message' => $e->getMessage()];
                break;
            }
        }

        return $results;
    }
}
