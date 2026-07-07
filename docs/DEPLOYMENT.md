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
