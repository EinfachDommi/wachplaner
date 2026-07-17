# Shield Testing

## S3 Formularschutz

### Honeypot

- leeres Honeypot-Feld wird akzeptiert
- befülltes Honeypot-Feld wird abgelehnt
- Array als Honeypot-Wert wird abgelehnt
- Feld ist visuell verborgen und für Tastaturnavigation deaktiviert

### Form-Token

- gültiges Token wird akzeptiert
- manipulierte Signatur wird abgelehnt
- falsche Formular-ID wird abgelehnt
- fehlendes Token wird abgelehnt
- Token vor Mindestalter wird abgelehnt
- abgelaufenes Token wird abgelehnt
- erneut verwendetes Token wird abgelehnt
- Token aus anderer Session wird abgelehnt
- Cleanup entfernt abgelaufene Tokens
- Anzahl offener Tokens ist begrenzt

### Kompatibilität

- bestehender Login funktioniert unverändert
- bestehende Registrierung funktioniert unverändert
- Guardian-Wartung funktioniert
- `/system` funktioniert
- `/system/health` funktioniert
- keine PHP-Warnings oder Stacktraces

## S4-Voraussetzung

Die Integration darf erst erfolgen, wenn S3 isoliert getestet ist.
