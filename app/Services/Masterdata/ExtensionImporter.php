<?php

namespace Wachplaner\Services\Masterdata;

final class ExtensionImporter extends AbstractImporter
{
    public function key(): string { return 'extensions'; }
    public function label(): string { return 'Erweiterungen'; }
    public function expectedColumns(): array { return ['Erweiterung','Wachtyp','Kosten','Bauzeit','Schaltet Fahrzeuge frei','Bemerkung']; }

    protected function importRow(array $row, ImportResult $result): void
    {
        $name = $this->value($row, 'erweiterung');
        if ($name === '') { $result->skipped++; return; }

        $stationType = $this->value($row, 'wachtyp', null);
        $slug = $this->slug($stationType . '_' . $name);
        $exists = $this->pdo->prepare('SELECT id FROM expansions WHERE slug=? LIMIT 1');
        $exists->execute([$slug]);
        $id = $exists->fetchColumn();

        if ($id) {
            $stmt = $this->pdo->prepare('UPDATE expansions SET name=?, station_type=?, cost=?, build_time=?, unlocks=?, notes=?, active=1 WHERE id=?');
            $stmt->execute([$name, $stationType, $this->intValue($this->value($row, 'kosten', 0)), $this->value($row, 'bauzeit', null), $this->value($row, 'schaltet fahrzeuge frei', null), $this->value($row, 'bemerkung', null), $id]);
            $result->updated++;
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO expansions(name,slug,station_type,cost,build_time,unlocks,notes,active) VALUES(?,?,?,?,?,?,?,1)');
            $stmt->execute([$name, $slug, $stationType, $this->intValue($this->value($row, 'kosten', 0)), $this->value($row, 'bauzeit', null), $this->value($row, 'schaltet fahrzeuge frei', null), $this->value($row, 'bemerkung', null)]);
            $result->created++;
        }
    }
}
