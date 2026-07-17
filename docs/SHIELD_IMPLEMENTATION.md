# Shield Implementation

## S1 – Sicherheitskern

Status: Implementiert und migriert.

- Request-ID
- SecurityContext
- SecurityEvent
- SecurityAuditLogger
- RegistrationPolicy

## S2 – Rate Limiting

Status: Implementiert und migriert.

- getrennte Scopes
- gehashte Subjekte
- transaktionssichere Zähler
- temporäre und eskalierende Sperren
- Cleanup
- Admin-Auswertung

## S3 – Formularschutz

Status: Implementiert, aber noch nicht in Login und Registrierung integriert.

Enthalten:

- HoneypotGuard
- signierte Form-Tokens
- Mindestdauer zwischen Darstellung und Absenden
- maximale Gültigkeitsdauer
- einmalige Token-Verwendung
- Session-basierter Replay-Schutz
- Cleanup abgelaufener Tokens
- zentraler FormProtectionService
- Factory für Konfigurationswerte

## Sicherheitsverhalten

- Ein befülltes Honeypot-Feld wird abgelehnt.
- Ein zu schnell abgesendetes Formular wird abgelehnt.
- Ein abgelaufenes Token wird abgelehnt.
- Ein erneut verwendetes Token wird als Replay abgelehnt.
- Tokens sind HMAC-signiert.
- Der geheime Schlüssel wird niemals im Formular ausgegeben.

## Nächster Schritt

Shield S4:

- Integration in Login
- Integration in Registrierung
- Rate-Limit-Verknüpfung
- Audit Events
- generische Fehlermeldungen
- Session-Regeneration
- AdminLTE-Sicherheitsübersicht
