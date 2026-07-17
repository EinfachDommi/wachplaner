<?php

declare(strict_types=1);

namespace Wachplaner\Services\Masterdata;

use InvalidArgumentException;
use PDO;
use Throwable;
use Wachplaner\Services\Logging\Logger;

final class MasterdataManager
{
    private array $importers;
    private ImportLogger $importLogger;
    private Validator $validator;

    public function __construct(private readonly PDO $pdo)
    {
        $reader = new SimpleXlsxReader();

        $this->importers = [
            'vehicles' => new VehicleImporter($pdo, $reader),
            'trainings' => new TrainingImporter($pdo, $reader),
            'extensions' => new ExtensionImporter($pdo, $reader),
            'building_costs' => new BuildingCostImporter($pdo, $reader),
        ];

        $logger = new Logger(__DIR__ . '/../../../storage/logs');
        $this->importLogger = new ImportLogger($pdo, $logger);
        $this->validator = new Validator();
    }

    public function importers(): array
    {
        return $this->importers;
    }

    public function importer(string $key): ImporterInterface
    {
        if (!isset($this->importers[$key])) {
            throw new InvalidArgumentException('Unbekannter Import-Typ: ' . $key);
        }

        return $this->importers[$key];
    }

    public function import(string $key, string $filePath, ?string $fileName = null): ImportResult
    {
        $validationErrors = $this->validator->validateImportFile($filePath, $fileName);

        if ($validationErrors !== []) {
            $result = new ImportResult($key);
            foreach ($validationErrors as $error) {
                $result->addError($error);
            }
            $this->importLogger->log($result, $fileName);
            return $result;
        }

        try {
            $result = $this->importer($key)->import($filePath);
        } catch (Throwable $exception) {
            $result = new ImportResult($key);
            $result->addError('Der Import konnte nicht ausgeführt werden: ' . $exception->getMessage());
        }

        $this->importLogger->log($result, $fileName);
        return $result;
    }

    public function latestLogs(int $limit = 20): array
    {
        return $this->importLogger->latest($limit);
    }

    public function counts(): array
    {
        return [
            'vehicle_types' => $this->countTableRows('vehicle_types'),
            'training_types' => $this->countTableRows('training_types'),
            'training_vehicle_requirements' => $this->countTableRows('training_vehicle_requirements'),
            'extensions' => $this->countTableRows('expansions'),
            'building_costs' => $this->countTableRows('station_build_costs'),
        ];
    }

    private function countTableRows(string $table): int
    {
        $allowedTables = [
            'vehicle_types',
            'training_types',
            'training_vehicle_requirements',
            'expansions',
            'station_build_costs',
        ];

        if (!in_array($table, $allowedTables, true)) {
            throw new InvalidArgumentException('Unzulässige Tabelle für Stammdatenzählung.');
        }

        return (int) $this->pdo
            ->query(sprintf('SELECT COUNT(*) FROM `%s`', $table))
            ->fetchColumn();
    }
}
