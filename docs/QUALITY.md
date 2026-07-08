# Quality Layer

## Ziel

Der Wachplaner soll technische Fehler kontrolliert behandeln und keine PHP-Fatal-Fehler an Benutzer ausgeben.

## Regeln

- Konfiguration wird ausschließlich über `EnvLoader` und `Config` gelesen.
- Datenbankzugriffe laufen über `Database::pdo()` und später über Repositories.
- Fehler werden über `ErrorHandler` abgefangen und mit Fehler-ID geloggt.
- Logs liegen unter `storage/logs/`.
- `/system` ist die erste Anlaufstelle zur Prüfung von DEV/PROD.

## Fehlercodes

| Code | Bedeutung |
|---|---|
| CFG-001 | Konfiguration fehlt oder ist unvollständig |
| DB-001 | Datenbankverbindung fehlgeschlagen |
| APP-001 | Unerwarteter Anwendungsfehler |

## DEV-Abnahmetest

1. `.env` vorhanden und vollständig.
2. `/system` zeigt alle kritischen Checks grün.
3. `/upgrade` kann mehrfach ausgeführt werden.
4. Fehler werden in `storage/logs/app.log` protokolliert.
5. Keine Stacktraces im Browser.
