# ADR 005 – Security Audit Log

## Status

Accepted

## Entscheidung

Sicherheitsereignisse werden getrennt von normalen Anwendungslogs in einer strukturierten
Datenbanktabelle gespeichert.

## Gespeicherte Informationen

- Ereignistyp
- Schweregrad
- Ergebnis
- Request-ID
- optionale Benutzer-ID
- gehashte Subjekt- und Netzwerkbezüge
- begrenzte Metadaten
- Zeitstempel

## Nicht gespeicherte Informationen

- Passwörter
- Session-Cookies
- CSRF-Token
- vollständige Einladungstoken
- vollständige Authorization-Header

## Begründung

Ein strukturiertes Audit Log erlaubt Filter, AdminLTE-Auswertung und nachvollziehbare
Entsperrentscheidungen, ohne normale Logs mit sensiblen Details anzureichern.
