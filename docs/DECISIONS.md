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

## ADR-0001 – Fahrzeugtypen sind primäre Realbau-Stammdaten

**Status:** Akzeptiert  
**Datum:** 2026-07-07

Die Fahrzeugtypen aus dem Leitstellenspiel werden im Wachplaner als primäre Fahrzeug-Stammdaten verwendet. Es wird keine zusätzliche Tabelle `vehicle_types_real` eingeführt.

### Begründung

- Die aktuell verwendeten Fahrzeugklassen sind für den geplanten Realbau-Ansatz ausreichend realitätsnah.
- Eine zusätzliche Mapping-Ebene würde die Stammdatenpflege unnötig erschweren.
- Der spätere Soll-/Ist-Abgleich kann direkt gegen die LSS-Fahrzeugtypen erfolgen.
- Fahrzeugdaten haben damit genau eine fachliche Quelle.

## ADR-0002 – Stammdaten werden nicht mehr fest in Migrationen gepflegt

**Status:** Akzeptiert  
**Datum:** 2026-07-07

Migrationen erstellen künftig Tabellen und technische Strukturen. Fachliche Stammdaten werden über Importer aus Excel-Dateien gepflegt.

### Begründung

- Änderungen an Fahrzeugtypen, Ausbildungen, Erweiterungen und Baukosten sollen ohne SQL-Bearbeitung möglich sein.
- Die Excel-Dateien bleiben die fachliche Single Source of Truth.
- Updates durch neue Leitstellenspiel-Inhalte werden einfacher nachvollziehbar.

## ADR-0003 – Keine ungefangenen technischen Fehler im Browser

**Status:** Akzeptiert  
**Datum:** 2026-07-08

Technische Fehler dürfen nicht als PHP-Fatal-Error oder Stacktrace im Browser erscheinen. Der Wachplaner zeigt stattdessen eine verständliche Fehlerseite mit Fehlercode und Fehler-ID. Details werden unter `storage/logs/` protokolliert.

### Begründung

- Produktive Systeme dürfen keine internen Pfade, SQL-Details oder Stacktraces offenlegen.
- Fehler-IDs erleichtern Support und Fehlersuche.
- DEV und PROD verhalten sich konsistenter.

## ADR-0004 – Upgrade-Migrationen laufen ohne PDO-Transaktion

**Status:** Akzeptiert  
**Datum:** 2026-07-08

SQL-Upgrades werden bei MySQL/MariaDB ohne explizite PDO-Transaktion ausgeführt.

### Begründung

- DDL-Statements wie `CREATE TABLE` und `ALTER TABLE` lösen implizite Commits aus.
- Explizite Transaktionen können dadurch mit `There is no active transaction` fehlschlagen.
- Migrationen werden stattdessen einzeln protokolliert und nach erfolgreicher Ausführung in `system_migrations` eingetragen.
