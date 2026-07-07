<?php

namespace Wachplaner\Services\Masterdata;

final class TrainingImporter extends AbstractImporter
{
    public function key(): string { return 'training_types'; }
    public function label(): string { return 'Ausbildungen'; }
    public function expectedColumns(): array { return ['Ausbildung','Organisation','Ausbildungsstätte','Dauer (Tage)','Benötigt für Fahrzeug(e)']; }

    protected function importRow(array $row, ImportResult $result): void
    {
        $name = $this->value($row, 'ausbildung');
        if ($name === '') { $result->skipped++; return; }

        // Bekannter Tippfehler in der Quelldatei wird beim Import normalisiert.
        if ($name === 'Dienstgrruppenleitung') {
            $name = 'Dienstgruppenleitung';
        }

        $organisation = $this->value($row, 'organisation', null);
        $school = $this->value($row, 'ausbildungsstätte', null);
        $slug = $this->slug($name);
        $duration = $this->intValue($this->value($row, 'dauer (tage)', 0));
        $vehicle = $this->value($row, 'benötigt für fahrzeug(e)', '');

        $exists = $this->pdo->prepare('SELECT id FROM training_types WHERE slug=? AND COALESCE(organisation,"")=COALESCE(?,"") AND COALESCE(school,"")=COALESCE(?,"") LIMIT 1');
        $exists->execute([$slug, $organisation, $school]);
        $trainingId = $exists->fetchColumn();

        if ($trainingId) {
            $stmt = $this->pdo->prepare('UPDATE training_types SET name=?, duration_days=? WHERE id=?');
            $stmt->execute([$name, $duration, $trainingId]);
            $result->updated++;
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO training_types(name,slug,organisation,school,duration_days) VALUES(?,?,?,?,?)');
            $stmt->execute([$name, $slug, $organisation, $school, $duration]);
            $trainingId = (int)$this->pdo->lastInsertId();
            $result->created++;
        }

        if ($vehicle !== '') {
            $this->pdo->prepare('DELETE FROM training_vehicle_requirements WHERE training_type_id=? AND vehicle_name=?')->execute([$trainingId, $vehicle]);
            $this->pdo->prepare('INSERT INTO training_vehicle_requirements(training_type_id,vehicle_name) VALUES(?,?)')->execute([$trainingId, $vehicle]);
        }
    }
}
