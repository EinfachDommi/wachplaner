# Security

## Guardian

- Wartungsantworten geben keine Stacktraces, Zugangsdaten oder Serverpfade aus.
- Automatische Ausfälle werden lokal protokolliert.
- Der Circuit Breaker reduziert wiederholte Verbindungsversuche.
- Session-Cookies verwenden `HttpOnly`, `Secure` und `SameSite`.
- Manuelle Wartung erlaubt ausschließlich Administratoren Vollzugriff.
- Registrierung ist während jeder aktiven Wartung gesperrt.

## Nächster Schritt: Shield V0.2.3

- Rate Limiting
- Honeypot
- Formular-Mindestzeit
- Login-Sperren
- E-Mail-Verifikation
- Security Audit Log
