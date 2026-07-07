# Wachplaner V0.1 Realbau Edition

Domain: `http://wachplaner.sh-com.de`

Diese Version ist die erste saubere Realbau-Codebasis für die Planung von Wachen, Fahrzeugen, Erweiterungen, Baukosten und Ausbildungen für Leitstellenspiel.de.

## Enthalten

- PHP/PDO Grundsystem ohne Framework-Zwang
- Login/Register mit `password_hash()` und `password_verify()`
- CSRF-Schutz
- Projektverwaltung mit benannter Leitstelle je Projekt
- Stammdaten direkt in MySQL importierbar
- Fahrzeugtypen: 201 Datensätze
- Ausbildungen: 12 Zuordnungen
- Erweiterungen: 68 Datensätze
- Baukosten bis 10.000 Wachen: 80000 Preiszeilen
- Dashboard und Stammdatenansicht

## Installation

1. Dateien auf den Webspace hochladen.
2. Entweder den DocumentRoot direkt auf `public/` setzen **oder** das Projekt unverändert hochladen; die neue Root-`.htaccess` leitet automatisch nach `public/` weiter.
3. Datenbankdaten in `config/config.php` anpassen oder Umgebungsvariablen setzen.
4. `http://wachplaner.sh-com.de/install` öffnen. Falls der Server den DocumentRoot direkt auf `public/` setzt, funktioniert derselbe Pfad ebenfalls.
5. Datenbank installieren/aktualisieren.
6. Danach unter `/register` den ersten Admin-Benutzer anlegen.

## .htaccess / Public-Root

Das Projekt enthält jetzt zwei `.htaccess`-Dateien:

- `/.htaccess` leitet alle Anfragen intern auf `public/index.php` weiter.
- `/public/.htaccess` übernimmt das Routing innerhalb der Anwendung.

Damit kann die Anwendung auch dann unter `http://wachplaner.sh-com.de` laufen, wenn der Webspace-DocumentRoot nicht direkt auf den Ordner `public/` gesetzt werden kann.

## CLI-Installation

```bash
php scripts/migrate.php
```

## Nächste Version

V0.2: Leitstellenspiel-Login, Session-Cookie, JSON-Cache und erster API-Sync.
