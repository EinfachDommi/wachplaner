# Shield Implementation

## S1 – Sicherheitskern

Status: Implementiert und migriert.

- Request-ID
- SecurityContext
- SecurityEvent
- SecurityAuditLogger
- RegistrationPolicy

## S2 – Rate Limiting

Status: Implementiert, aber noch nicht in Login und Registrierung integriert.

Enthalten:

- getrennte Scopes
- normalisierte und gehashte Subjekte
- konfigurierbare Profile
- transaktionssichere Zähler
- temporäre Sperren
- abgestufte Sperrdauer
- Cleanup alter Einträge
- Admin-Auswertung aktiver Sperren
- sichere Standardwerte in `system_settings`

## Nächster Schritt

Shield S3:

- HoneypotGuard
- signierte Formularzeit
- Replay-Vorbereitung
- sichere Integration in Registrierungsformulare
