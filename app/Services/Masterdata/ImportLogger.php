<?php

namespace Wachplaner\Services\Masterdata;

use PDO;
use Wachplaner\Services\Logging\Logger;

final class ImportLogger
{
    public function __construct(private PDO $pdo, private Logger $logger)
    {
        $this->ensureTable();
    }

    public function log(ImportResult $result, ?string $fileName = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO masterdata_import_logs(import_type,file_name,processed,created_count,updated_count,skipped_count,error_count,errors_json,created_at) VALUES(?,?,?,?,?,?,?,?,NOW())');
        $stmt->execute([
            $result->type,
            $fileName,
            $result->processed,
            $result->created,
            $result->updated,
            $result->skipped,
            count($result->errors),
            json_encode($result->errors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        $this->logger->info('Masterdata import finished', [
            'type' => $result->type,
            'file' => $fileName,
            'processed' => $result->processed,
            'created' => $result->created,
            'updated' => $result->updated,
            'skipped' => $result->skipped,
            'errors' => count($result->errors),
        ]);
    }

    public function latest(int $limit = 20): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM masterdata_import_logs ORDER BY created_at DESC, id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function ensureTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS masterdata_import_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            import_type VARCHAR(80) NOT NULL,
            file_name VARCHAR(255) NULL,
            processed INT NOT NULL DEFAULT 0,
            created_count INT NOT NULL DEFAULT 0,
            updated_count INT NOT NULL DEFAULT 0,
            skipped_count INT NOT NULL DEFAULT 0,
            error_count INT NOT NULL DEFAULT 0,
            errors_json JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
}
