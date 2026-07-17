# Shield Implementation

## S1 – Sicherheitskern

Status: Implementiert, aber noch nicht in Login oder Registrierung integriert.

Enthalten:

- Request-ID
- SecurityContext
- SecurityEvent
- SecurityAuditLogger
- RegistrationPolicy
- Security-Audit-Migration
- Registrierungsmodus in `system_settings`

## Sicherheitsregeln

- Keine Passwörter, Tokens, Cookies oder CSRF-Werte im Audit Log
- Netzwerk- und Benutzerbezüge nur gehasht
- Audit-Fehler brechen Requests nicht ab
- Wartungsmodus erzwingt deaktivierte Registrierung
- Standardmodus nach Upgrade: `disabled`

## Nächster Schritt

Shield S2:

- RateLimiter
- RateLimitRepository
- Login- und Registrierungsprofile
- temporäre Sperren
- Cleanup-Strategie
