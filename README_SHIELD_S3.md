# Shield S3 – Formularschutz

## Installation

1. Patch pfadtreu auf `develop` kopieren.
2. Keine Datenbankmigration erforderlich.
3. `.env` um folgende Werte ergänzen:

```env
SECURITY_FORM_SECRET=
SECURITY_FORM_MINIMUM_AGE=2
SECURITY_FORM_MAXIMUM_AGE=7200
SECURITY_HONEYPOT_FIELD=website
```

`SECURITY_FORM_SECRET` muss mindestens 32 zufällige Zeichen besitzen.
Alternativ kann die Anwendung einen bereits vorhandenen `APP_KEY` verwenden.

## Wichtig

S3 verändert Login und Registrierung noch nicht.
Die Integration erfolgt kontrolliert in S4.
