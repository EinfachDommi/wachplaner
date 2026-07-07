# Architecture Decisions

Dieses Dokument hält wichtige fachliche und technische Entscheidungen fest.

## ADR-001: Projekt als oberste Planungsebene

**Entscheidung:** Der Wachplaner organisiert Realbau über Projekte.

**Begründung:** Realbau erfolgt regions- oder organisationsbezogen. Ein Projekt bündelt Leitstelle, Wachen, Soll-Fahrzeuge, Erweiterungen, Kosten und später den Soll-/Ist-Abgleich.

## ADR-002: Keine Tabelle `vehicle_types_real`

**Entscheidung:** Es gibt zunächst nur eine zentrale Tabelle `vehicle_types`.

**Begründung:** Die Fahrzeugklassen im Leitstellenspiel sind für die geplante Realbauweise ausreichend realitätsnah. Eine zusätzliche Mapping-Ebene würde die V0.x unnötig komplex machen.

## ADR-003: JSON-Cache vor MySQL-Synchronisation

**Entscheidung:** LSS-Daten werden später zuerst als JSON gespeichert und danach in MySQL synchronisiert.

**Begründung:** Dadurch werden Serveranfragen reduziert, API-Ausfälle abgefedert und Sync-Läufe nachvollziehbarer.

## ADR-004: Leitstelle als benannter Datensatz, nicht als Typkatalog

**Entscheidung:** Es gibt keine separate Leitstellentypenliste.

**Begründung:** Für die Realbauplanung reicht pro Projekt eine oder mehrere benannte Leitstellen. Der eigentliche Typ ist fachlich nicht entscheidend.

## ADR-005: V0.1.2 ist Stable Base

**Entscheidung:** V0.1.2 bleibt als stabile Basis bestehen. Neue Entwicklung erfolgt ab V0.2 über Git und Sprints.

**Begründung:** So bleibt ein funktionierender Rückfallstand erhalten.
