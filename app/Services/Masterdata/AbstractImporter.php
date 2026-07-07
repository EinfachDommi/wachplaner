<?php

namespace Wachplaner\Services\Masterdata;

use PDO;
use Throwable;

abstract class AbstractImporter implements ImporterInterface
{
    public function __construct(protected PDO $pdo, protected SimpleXlsxReader $reader)
    {
    }

    public function import(string $filePath): ImportResult
    {
        $result = new ImportResult($this->key());

        try {
            $rows = $this->readRows($filePath);
            $this->pdo->beginTransaction();
            foreach ($rows as $rowNumber => $row) {
                $result->processed++;
                try {
                    $this->importRow($row, $result);
                } catch (Throwable $exception) {
                    $result->skipped++;
                    $result->addError('Zeile ' . ($rowNumber + 2) . ': ' . $exception->getMessage());
                }
            }

            if ($result->success()) {
                $this->pdo->commit();
            } else {
                $this->pdo->rollBack();
            }
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $result->addError($exception->getMessage());
        }

        return $result;
    }

    abstract protected function importRow(array $row, ImportResult $result): void;

    protected function readRows(string $filePath): array
    {
        return $this->reader->readAssoc($filePath, $this->headerRowIndex());
    }

    protected function headerRowIndex(): int
    {
        return 0;
    }

    protected function value(array $row, array|string $keys, mixed $default = ''): mixed
    {
        foreach ((array)$keys as $key) {
            $normalized = mb_strtolower(trim($key), 'UTF-8');
            if (array_key_exists($normalized, $row) && $row[$normalized] !== '') {
                return trim((string)$row[$normalized]);
            }
        }

        return $default;
    }

    protected function intValue(mixed $value): int
    {
        $value = str_replace(['.', ',', ' Credits', 'credits'], ['', '.', '', ''], (string)$value);
        return (int)round((float)$value);
    }

    protected function slug(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = strtr($value, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $value = preg_replace('/[^a-z0-9]+/u', '_', $value);
        return trim((string)$value, '_') ?: 'n_a';
    }
}
