# Security

## Guardian

- Wartungsantworten geben keine Stacktraces, Zugangsdaten oder Serverpfade aus.
- Automatische Ausfälle werden lokal protokolliert.
- Der Circuit Breaker reduziert wiederholte Datenbankverbindungen während eines Ausfalls.
- Session-Cookies verwenden `HttpOnly`, `Secure` und `SameSite`.
- Manuelle Wartung erlaubt ausschließlich Administratoren Vollzugriff.
- Registrierung ist während jeder aktiven Wartung gesperrt.

## Shield

Shield schützt öffentliche Authentifizierungs- und Registrierungsendpunkte gegen Bots,
Brute Force, Benutzer-Erkennung und wiederholte Formularübermittlung.

### Verbindliche Grundregeln

1. Jede öffentliche Schreiboperation besitzt CSRF-Schutz.
2. Rate Limits werden serverseitig erzwungen.
3. Schutzmechanismen verwenden keine Passwörter oder vollständigen Tokens als Schlüssel.
4. Fehlermeldungen verraten nicht, ob ein Benutzerkonto existiert.
5. Sicherheitsereignisse werden nachvollziehbar, aber datensparsam protokolliert.
6. Wartungsmodus deaktiviert Registrierung unabhängig von der normalen Systemeinstellung.
7. CAPTCHA ist adaptiv und nicht standardmäßig für jeden Benutzer erforderlich.
8. Ein erfolgreicher Login regeneriert die Session-ID.
9. Gesperrte Benutzer oder IPs können nur durch berechtigte Administratoren entsperrt werden.
10. Sicherheitslogs enthalten keine Zugangsdaten, Session-Cookies oder CSRF-Token.

## Nächster Schritt

Die Implementierung erfolgt auf `develop` als V0.2.3-dev und wird erst nach vollständiger
DEV-Abnahme als Shield abgeschlossen.
