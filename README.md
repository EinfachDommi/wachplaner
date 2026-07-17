# Wachplaner V0.2.2-dev – Guardian

Realbau-Planungs- und Controllingsystem für Leitstellenspiel.de.

## Umgebungen

- Produktion: `https://wachplaner.sh-com.de` (`main`)
- Entwicklung: `https://dev.wachplaner.sh-com.de` (`develop`)

Dieser Stand ist die aktuelle Entwicklungsbasis. Er ist noch kein finales Guardian-Release.

## Enthalten

- PHP/PDO-Grundsystem mit Login, CSRF-Schutz und Projektverwaltung
- Bootstrap 5.3 und AdminLTE 4
- Masterdata-Importer für Fahrzeugtypen, Ausbildungen, Erweiterungen und Baukosten
- Upgrade- und Migrationssystem
- Guardian-Wartungsmodus mit Adminzugang
- automatischer 503-Failover bei Datenbankausfall
- Circuit Breaker und Recovery-Erkennung
- modulares Health-Check-System
- System-Center mit Übersicht, Health, Wartung, Logs, Updates, Sicherheit und Einstellungen
- vorbereitete Feature Flags

## Bestehende Installation aktualisieren

1. Datenbank und Dateien sichern.
2. Patch pfadtreu auf die DEV-Installation kopieren.
3. Bestehende `.env` nicht überschreiben.
4. Fehlende Variablen aus `.env.example` ergänzen.
5. Als Administrator `https://dev.wachplaner.sh-com.de/upgrade` aufrufen.
6. Danach `/system` und `/system/health` prüfen.

## Neuinstallation

1. Vollständiges Projekt auf den Webspace laden.
2. `.env.example` nach `.env` kopieren und konfigurieren.
3. DocumentRoot auf `public/` setzen oder die Root-`.htaccess` verwenden.
4. `/install` aufrufen.

## Guardian-Konfiguration

```env
MAINTENANCE_FORCE=false
MAINTENANCE_MESSAGE="Der Wachplaner wird aktuell gewartet."
MAINTENANCE_RETRY_SECONDS=30
REGISTRATION_ENABLED=true
SESSION_SECURE=true
SESSION_SAME_SITE=Lax
```

`MAINTENANCE_FORCE=true` aktiviert eine datenbankunabhängige Notfall-Wartungsseite.

## Nächste Atlas-Schritte

- V0.2.3 Shield: Anti-Spam, Rate Limiting und Registrierungsschutz
- V0.2.4 Integrity: Duplikatschutz und Idempotenz
- V0.2.5 Atlas Final: vollständige Abnahme und Produktivrelease
