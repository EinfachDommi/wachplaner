# Atlas Roadmap

Atlas stabilisiert den technischen Unterbau des Wachplaners. Bis zum finalen Atlas-Release werden keine Leitstellenspiel-Synchronisation und keine neuen Planungsfunktionen integriert.

## V0.2.2 – Guardian

### Ziel

Sicherer und nachvollziehbarer Systembetrieb.

### Status

- [x] Manueller Wartungsmodus
- [x] Automatischer Datenbank-Failover
- [x] Datenbankunabhängige 503-Seite
- [x] Circuit Breaker und Recovery-Logging
- [x] Health-Endpunkt
- [x] Modularer System-Health-Core
- [x] Prüfungen für Konfiguration, Datenbank, Storage, PHP-Erweiterungen und Migrationen
- [x] Zentrale Versions- und Statusobjekte
- [ ] Vollständige Abnahme auf DEV
- [ ] Störung und Wiederherstellung der Datenbank testen
- [ ] Rechte und Wartungszugriff testen

## V0.2.3 – Shield

### Ziel

Schutz vor automatisiertem Missbrauch und unberechtigten Zugriffen.

- [ ] Registrierung zentral aktivierbar
- [ ] Honeypot
- [ ] Mindestdauer für Formulare
- [ ] Login- und Registrierungs-Rate-Limits
- [ ] abgestufte Sperrzeiten
- [ ] neutrale Authentifizierungsfehler
- [ ] Audit-Log
- [ ] adaptive Bot-Prüfung

## V0.2.4 – Integrity

### Ziel

Datenintegrität und idempotente Schreibvorgänge.

- [ ] normalisierte Vergleichswerte
- [ ] eindeutige Datenbank-Constraints
- [ ] Idempotency Keys für kritische Formulare
- [ ] Import-Duplikatschutz
- [ ] Konfliktberichte
- [ ] Bereinigung vorhandener Duplikate

## V0.2.5 – Atlas Final

### Ziel

Releasefähiges Grundsystem.

- [ ] Neuinstallation
- [ ] Upgrade von V0.1.2
- [ ] wiederholtes Upgrade ohne Nebenwirkungen
- [ ] alle Stammdatenimporte
- [ ] Sicherheitsabnahme
- [ ] responsive UI-Abnahme
- [ ] Backup und Restore
- [ ] Release Candidate auf DEV
- [ ] Merge nach `main`
- [ ] produktives Deployment
