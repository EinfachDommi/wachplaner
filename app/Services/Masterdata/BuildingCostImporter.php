<?php

namespace Wachplaner\Services\Masterdata;

final class BuildingCostImporter extends AbstractImporter
{
    public function key(): string { return 'building_costs'; }
    public function label(): string { return 'Baukosten'; }
    public function expectedColumns(): array { return ['Wachenanzahl']; }

    protected function headerRowIndex(): int
    {
        // Die Baukostendatei enthält in Zeile 1 einen Hinweistext, die Kopfzeile steht in Zeile 2.
        return 1;
    }

    protected function importRow(array $row, ImportResult $result): void
    {
        $stationCount = $this->intValue($this->value($row, 'wachenanzahl', 0));
        if ($stationCount <= 0) { $result->skipped++; return; }

        $columns = [
            'normale feuerwache' => 'Feuerwache',
            'kleine feuerwache' => 'Feuerwache (Kleinwache)',
            'feuerwachen- spezialisierungen' => 'Feuerwachen-Spezialisierungen',
            'technisches hilfs-werk' => 'THW-Ortsverband',
            'normale polizeiwache' => 'Polizeiwache',
            'kleine polizeiwache' => 'Polizeiwache (Kleinwache)',
            'berg rettungswache' => 'Bergrettungswache',
            'seenot- rettungswache' => 'Seenot-Rettungswache',
        ];

        foreach ($columns as $sourceColumn => $stationType) {
            $cost = $this->intValue($this->value($row, $sourceColumn, 0));
            if ($cost <= 0) { continue; }
            $stmt = $this->pdo->prepare('INSERT INTO station_build_costs(station_count,station_type,cost) VALUES(?,?,?) ON DUPLICATE KEY UPDATE cost=VALUES(cost)');
            $stmt->execute([$stationCount, $stationType, $cost]);
            $result->updated++;
        }
    }
}
