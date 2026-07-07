# Sprints

## Sprint 2 – Atlas / Masterdata Foundation

**Status:** gestartet  
**Branch:** `develop`

### Ziel

Der Wachplaner erhält eine Stammdaten-Engine. Fahrzeugtypen, Ausbildungen, Erweiterungen und Baukosten werden über Importer gepflegt und nicht mehr manuell in SQL bearbeitet.

### Arbeitspakete

- Masterdata Manager
- XLSX Reader
- Importer für Fahrzeugtypen
- Importer für Ausbildungen
- Importer für Erweiterungen
- Importer für Baukosten
- Import-Logging
- Adminseite für Stammdatenimport
- CLI-Importskript

### Definition of Done

- Alle vorhandenen Excel-Stammdaten können importiert werden.
- Importläufe werden protokolliert.
- Fehlerhafte Zeilen führen nicht zu stillen Datenfehlern.
- Dashboard/Systemstatus zeigen Stammdatenzählungen.
- Architekturentscheidung zur Vermeidung einer `vehicle_types_real`-Tabelle ist dokumentiert.
