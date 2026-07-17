<?php

declare(strict_types=1);

namespace Wachplaner\Services\Masterdata;

use PDO;
use Throwable;
use Wachplaner\Services\Logging\Logger;

final class ImportLogger
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly Logger $logger
    ) {
    }

    public function log(ImportResult $result, ?string $fileName = null): void
    {
        $status = $result->status();
        $message = $this->buildMessage($result);

        try {
            $statement = $this->pdo->prepare(
                <<<'SQL'
                INSERT INTO masterdata_import_logs (
                    import_type,
                    source_file,
                    status,
                    rows_total,
                    rows_created,
                    rows_updated,
                    rows_skipped,
                    message,
                    created_at
                ) VALUES (
                    :import_type,
                    :source_file,
                    :status,
                    :rows_total,
                    :rows_created,
                    :rows_updated,
                    :rows_skipped,
                    :message,
                    NOW()
                )
                SQL
            );

            $statement->execute([
                'import_type' => $result->type,
                'source_file' => $fileName,
                'status' => $status,
                'rows_total' => $result->processed,
                'rows_created' => $result->created,
                'rows_updated' => $result->updated,
                'rows_skipped' => $result->skipped,
                'message' => $message,
            ]);
        } catch (Throwable $exception) {
            $this->writeFileLogSafely('error', 'Masterdata import log could not be persisted', [
                'type' => $result->type,
                'file' => $fileName,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
            return;
        }

        $this->writeFileLogSafely(
            $status === 'error' ? 'error' : 'info',
            'Masterdata import finished',
            [
                'type' => $result->type,
                'file' => $fileName,
                'status' => $status,
                'processed' => $result->processed,
                'created' => $result->created,
                'updated' => $result->updated,
                'skipped' => $result->skipped,
                'errors' => count($result->errors),
                'warnings' => count($result->warnings),
            ]
        );
    }

    public function latest(int $limit = 20): array
    {
        $limit = max(1, min($limit, 100));

        $statement = $this->pdo->prepare(
            <<<'SQL'
            SELECT
                id,
                import_type,
                source_file,
                status,
                rows_total,
                rows_created,
                rows_updated,
                rows_skipped,
                message,
                created_at
            FROM masterdata_import_logs
            ORDER BY created_at DESC, id DESC
            LIMIT :limit
            SQL
        );

        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        return is_array($rows) ? $rows : [];
    }

    private function buildMessage(ImportResult $result): string
    {
        $parts = [
            sprintf('%d Datensätze verarbeitet.', $result->processed),
            sprintf('%d erstellt.', $result->created),
            sprintf('%d aktualisiert.', $result->updated),
            sprintf('%d übersprungen.', $result->skipped),
        ];

        if ($result->errors !== []) {
            $parts[] = sprintf('%d Fehler.', count($result->errors));
            $parts[] = 'Fehler: ' . implode(' | ', array_slice($result->errors, 0, 5));
        } elseif ($result->warnings !== []) {
            $parts[] = sprintf('%d Warnungen.', count($result->warnings));
            $parts[] = 'Warnungen: ' . implode(' | ', array_slice($result->warnings, 0, 5));
        } else {
            $parts[] = 'Import erfolgreich.';
        }

        return implode(' ', $parts);
    }

    private function writeFileLogSafely(string $level, string $message, array $context): void
    {
        try {
            if ($level === 'error') {
                $this->logger->error($message, $context);
                return;
            }
            $this->logger->info($message, $context);
        } catch (Throwable) {
        }
    }
}
