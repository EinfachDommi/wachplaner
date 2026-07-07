<?php

namespace Wachplaner\Services\Masterdata;

final class VehicleImporter extends AbstractImporter
{
    public function key(): string { return 'vehicle_types'; }
    public function label(): string { return 'Fahrzeugtypen'; }
    public function expectedColumns(): array { return ['LSS-Fahrzeug','Kategorie','Benötigte Wache','Erweiterung','Personal','Ausbildung','Credits']; }

    protected function importRow(array $row, ImportResult $result): void
    {
        $name = $this->value($row, 'lss-fahrzeug');
        if ($name === '') { $result->skipped++; return; }

        $slug = $this->slug($name);
        $exists = $this->pdo->prepare('SELECT id FROM vehicle_types WHERE slug = ? LIMIT 1');
        $exists->execute([$slug]);
        $id = $exists->fetchColumn();

        if ($id) {
            $stmt = $this->pdo->prepare('UPDATE vehicle_types SET name=?, category=?, station_type=?, required_expansion=?, personnel_required=?, required_training=?, credits=?, active=1 WHERE id=?');
            $stmt->execute([
                $name,
                $this->value($row, 'kategorie', null),
                $this->value($row, 'benötigte wache', null),
                $this->value($row, 'erweiterung', null),
                $this->intValue($this->value($row, 'personal', 0)),
                $this->value($row, 'ausbildung', null),
                $this->intValue($this->value($row, 'credits', 0)),
                $id,
            ]);
            $result->updated++;
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO vehicle_types(name,slug,category,station_type,required_expansion,personnel_required,required_training,credits,active) VALUES(?,?,?,?,?,?,?,?,1)');
            $stmt->execute([
                $name,
                $slug,
                $this->value($row, 'kategorie', null),
                $this->value($row, 'benötigte wache', null),
                $this->value($row, 'erweiterung', null),
                $this->intValue($this->value($row, 'personal', 0)),
                $this->value($row, 'ausbildung', null),
                $this->intValue($this->value($row, 'credits', 0)),
            ]);
            $result->created++;
        }
    }
}
