# Wachplaner

Realbau Planungs- und Controllingsystem für Leitstellenspiel.de.

## Aktuelle stabile Basis

Version: 0.1.2

## Aktueller Entwicklungszweig

Version: 0.2.0-dev

## Fokus 0.2.0

- Dokumentation professionalisieren
- Service-Struktur vorbereiten
- LSS-Login und Sessionverwaltung planen
- JSON-Cache vorbereiten
- Sync-Logging vorbereiten

## Nicht-Ziel

Dienstplanung ist kein Bestandteil des Projekts.


## Aktueller Sprint: Core Foundation

Ziel: Repository und Codebasis für die Entwicklung ab V0.2 professionalisieren.

### Erledigt

- GitHub Templates vorbereitet.
- Coding Guidelines ergänzt.
- Architecture Decisions eingeführt.
- Systemstatus-Seite ergänzt.
- CLI-Systemcheck ergänzt.
- Logging-Service vorbereitet.

### Nächster Sprint

Masterdata-Importer und Planning-Service vorbereiten.

## Aktueller Sprint: Sprint 2 – Atlas / Masterdata Foundation

Ziel: Stammdaten werden künftig über Importer gepflegt. Die Excel-Dateien für Fahrzeugtypen, Ausbildungen, Erweiterungen und Baukosten werden zur fachlichen Quelle der Stammdaten.

### Erledigt in diesem Zwischenstand

- Masterdata-Service-Struktur
- XLSX-Reader
- Importer-Klassen
- Admin-Importseite
- CLI-Import
- Import-Logging
- Dokumentation der Architekturentscheidungen

## Aktueller Fokus: V0.2.1 Atlas Hotfix

Ziel: Atlas stabilisieren, bevor neue Features wie die Leitstellenspiel-Synchronisation gebaut werden.

### Erledigt
- EnvLoader eingeführt
- Config-Klasse eingeführt
- Datenbankfehler abgefangen
- zentrale Fehlerseite ergänzt
- Upgrade-Migration für Versionsmetadaten ergänzt

### Nächste Prüfung auf DEV
- `.env` auf DEV mit `.env.example` abgleichen
- `/upgrade` ausführen
- `/system` prüfen

## V0.2.1 Atlas Quality Layer

Ziel: Atlas vor neuen Features weiter härten.

### Erledigt
- `DB_PORT` in Datenbankverbindung aufgenommen.
- Fehler-ID für ErrorHandler ergänzt.
- Mehrkanal-Logger ergänzt.
- Upgrade-Logging ergänzt.
- SystemCheckService eingeführt.
- Qualitätsdokumentation ergänzt.
- Known-Bugs-Liste eingeführt.

### DEV-Abnahme
- `/system` prüfen.
- `/upgrade` erneut ausführen.
- `storage/logs/upgrade.log` prüfen.
- Fehlerseite optional durch fehlerhafte `.env` auf DEV testen und danach `.env` wieder korrigieren.

## Atlas UI Refactoring

- [x] Bootstrap 5.3 / AdminLTE 4 Grundlayout
- [x] Responsive Sidebar und Navbar
- [x] Dashboard-Widgets
- [x] Auth-Layout
- [x] Dark-Mode-Vorbereitung
- [ ] DEV-Abnahmetest
- [ ] Atlas nach erfolgreichem Test abschließen


## V0.2.2 Guardian

Status: Implementiert, DEV-Abnahme ausstehend.

- administrativer Wartungsmodus
- automatischer Datenbank-Failover
- Circuit Breaker
- Recovery-Erkennung
- Health-Endpunkt
- statische 503-Seite
- system_settings Migration
