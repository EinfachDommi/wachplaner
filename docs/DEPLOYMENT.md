# Deployment

## Domain

Produktivdomain:

```text
http://wachplaner.sh-com.de
```

## Webroot

Empfohlen ist `public/` als DocumentRoot.

Alternativ leitet die Root-`.htaccess` alle Requests nach `public/` weiter.

## Installation

1. Dateien hochladen
2. `.env` aus `.env.example` erstellen
3. Datenbankdaten eintragen
4. `/install` aufrufen
5. Admin-Account erstellen

## Updates

Ab Version 0.2 sollen Updates über Migrationen laufen. Vor jedem Update sollte ein Datei- und Datenbankbackup erstellt werden.

## Cronjobs

Ab Version 0.2 geplant:

```bash
php scripts/lss_sync.php
```

## Upgrade einer bestehenden DEV-Installation

Wenn auf `dev.wachplaner.sh-com.de` bereits V0.1.2 läuft, wird V0.2 Atlas nicht über `/install`, sondern über `/upgrade` aktualisiert.

Ablauf:

1. Dateien aus dem `develop`-Stand auf die DEV-Subdomain hochladen.
2. Bestehende `.env` nicht überschreiben.
3. Im Browser anmelden.
4. `https://dev.wachplaner.sh-com.de/upgrade` öffnen.
5. Ausstehende Migrationen prüfen.
6. Upgrade starten.
7. Danach `https://dev.wachplaner.sh-com.de/system` und `https://dev.wachplaner.sh-com.de/admin/masterdata` prüfen.

`/install` ist nur für Neuinstallationen vorgesehen.
