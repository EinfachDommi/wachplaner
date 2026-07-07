<?php

namespace Wachplaner\Services\Masterdata;

use InvalidArgumentException;
use PDO;
use Wachplaner\Services\Logging\Logger;

final class MasterdataManager
{
    private array $importers;
    private ImportLogger $importLogger;

    public function __construct(private PDO $pdo)
    {
        $reader = new SimpleXlsxReader();
        $this->importers = [
            'vehicles' => new VehicleImporter($pdo, $reader),
            'trainings' => new TrainingImporter($pdo, $reader),
            'extensions' => new ExtensionImporter($pdo, $reader),
            'building_costs' => new BuildingCostImporter($pdo, $reader),
        ];
        $this->importLogger = new ImportLogger($pdo, new Logger(__DIR__ . '/../../../storage/logs'));
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
        $result = $this->importer($key)->import($filePath);
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
            'vehicle_types' => (int)$this->pdo->query('SELECT COUNT(*) FROM vehicle_types')->fetchColumn(),
            'training_types' => (int)$this->pdo->query('SELECT COUNT(*) FROM training_types')->fetchColumn(),
            'training_vehicle_requirements' => (int)$this->pdo->query('SELECT COUNT(*) FROM training_vehicle_requirements')->fetchColumn(),
            'extensions' => (int)$this->pdo->query('SELECT COUNT(*) FROM expansions')->fetchColumn(),
            'building_costs' => (int)$this->pdo->query('SELECT COUNT(*) FROM station_build_costs')->fetchColumn(),
        ];
    }
}
