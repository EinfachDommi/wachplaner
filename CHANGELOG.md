
## V0.2.0-dev Sprint 1 Core Foundation

- `.editorconfig` ergänzt.
- GitHub Issue- und Pull-Request-Templates ergänzt.
- `docs/CODING_GUIDELINES.md` ergänzt.
- `docs/DECISIONS.md` ergänzt.
- Systemstatus-Seite `/system` ergänzt.
- CLI-Systemcheck `scripts/system_check.php` ergänzt.
- Logger-Service vorbereitet.

# Changelog

## 0.2.0-dev

### Hinzugefügt
- `docs/`-Ordner mit Architektur-, Roadmap-, Datenbank-, API-, Stammdaten- und Deployment-Dokumentation
- vorbereitete Service-Struktur für LSS, Cache, Planning und Calculation
- `composer.json` für spätere PSR-4-Autoloading-Struktur
- `.gitignore` für produktive Entwicklung

### Geändert
- Projektstatus auf 0.2.0-dev gesetzt

## 0.1.2

### Stabil
- Root-`.htaccess` leitet auf `public/`
- Admin-Registrierung stabilisiert
- Stammdaten in Datenbank hinterlegt

## V0.2.0-dev – Sprint 2 Atlas

### Neu

- Masterdata-Engine eingeführt.
- XLSX-Reader ohne zusätzliche Composer-Abhängigkeit ergänzt.
- Importer für Fahrzeugtypen, Ausbildungen, Erweiterungen und Baukosten vorbereitet.
- Adminseite `/admin/masterdata` für Stammdatenimporte ergänzt.
- CLI-Import `scripts/import_masterdata.php` ergänzt.
- Import-Logging mit Tabelle `masterdata_import_logs` ergänzt.
- Dokumentation um `SPRINTS.md` und `VISION.md` erweitert.
- Architekturentscheidungen zu Fahrzeugtypen und Stammdatenpflege dokumentiert.

## 0.2.0-dev Atlas Upgrade-Flow

### Added
- Added `/upgrade` route for existing installations.
- Added `UpgradeRunner` with tracked SQL upgrade migrations.
- Added `database/upgrades/20260708_0200_atlas_masterdata.sql`.

### Changed
- Existing DEV installations should be updated through `/upgrade` instead of `/install`.

## V0.2.1 – Atlas Hotfix

### Fixed
- Fehlende oder abweichende `.env`-Dateien erzeugen keine ungefangenen PDO-Fatal-Errors mehr.
- Datenbankverbindungsfehler werden über eine verständliche Fehlerseite und `storage/logs/app.log` behandelt.
- `/install` und `/upgrade` werden geroutet, bevor die normale Anwendung eine Datenbankverbindung aufbaut.

### Changed
- Zentrales Konfigurationssystem mit `EnvLoader` und `Config` eingeführt.
- `.env.example` als Referenzdatei erweitert.
- Systemstatus um Version, Build, Umgebung und `.env`-Prüfung erweitert.

### Added
- Upgrade-Migration `20260708_0201_atlas_hotfix.sql` für Versions-/Build-Metadaten.
- `storage/version.json` als lokale Versionsinformation.
- `docs/COMMITS.md` mit GitHub-Desktop-Commitvorlage.


## V0.2.1 Hotfix Patch

### Fixed
- UpgradeRunner führt SQL-Migrationen ohne explizite PDO-Transaktion aus, da MySQL/MariaDB DDL-Statements implizite Commits auslösen können.
- Fehler `There is no active transaction` beim Aufruf von `/upgrade` behoben.

## V0.2.1 – Atlas Quality Layer

### Fixed
- `DB_PORT` wird jetzt aus `.env` berücksichtigt.
- Systemstatus nutzt einen eigenen `SystemCheckService` statt verstreuter Inline-Prüfungen.
- Upgrade-Läufe schreiben zusätzlich in `storage/logs/upgrade.log`.

### Changed
- Fehlerseiten enthalten jetzt eine eindeutige Fehler-ID zur Log-Zuordnung.
- Config kann Pflichtwerte zentral validieren.
- Logger unterstützt mehrere Kanäle (`app`, `upgrade`, später `lss`).
- `storage/version.json` wird nach erfolgreichen Upgrades aktualisiert.

### Added
- `docs/QUALITY.md` mit Qualitätsregeln, Fehlercodes und DEV-Abnahmetest.
- `docs/KNOWN_BUGS.md` als zentrale Bugliste.
