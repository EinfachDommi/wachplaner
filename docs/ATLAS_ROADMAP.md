# Atlas Roadmap

## Ziel

Atlas stellt den stabilen, sicheren und wartbaren Anwendungskern des Wachplaners bereit.
Bis zum Atlas-Final-Release werden keine Leitstellenspiel-Synchronisationsfunktionen entwickelt.

## V0.2.2 – Guardian

Betriebssicherheit und Wartungssteuerung:

- [x] Manueller Wartungsmodus über AdminLTE
- [x] Administratorzugriff während geplanter Wartung
- [x] Registrierung während Wartung gesperrt
- [x] Automatischer Wartungsmodus bei Datenbankausfall
- [x] Datenbankunabhängige 503-Seite
- [x] Lokaler Circuit Breaker
- [x] Automatische Recovery-Erkennung
- [x] `/system/health`
- [x] System- und Recovery-Logging
- [x] ENV-Notfall-Override
- [x] System-Center und modulare Health Checks
- [x] Abnahme auf DEV

## V0.2.3 – Shield

Schutz vor automatisiertem Missbrauch und missbräuchlichen Zugriffen:

### Planning

- [x] Bedrohungsmodell
- [x] Registrierungsrichtlinie
- [x] Login-Schutzkonzept
- [x] Honeypot- und Formularzeit-Konzept
- [x] Rate-Limit-Strategie
- [x] Audit-Log-Konzept
- [x] AdminLTE-Sicherheitsbereich geplant
- [x] Datenmodell und Migrationen geplant
- [x] Datenschutz- und Logging-Regeln festgelegt
- [x] Test- und Abnahmekriterien definiert

### Implementation

- [ ] Registrierung global steuerbar
- [ ] Optionaler Einladungsmodus
- [ ] Honeypot-Schutz
- [ ] Signierte Formularzeit
- [ ] IP- und Identitäts-basiertes Rate Limiting
- [ ] Abgestufte Login-Sperren
- [ ] Generische Login-Fehlermeldungen
- [ ] Session-Regeneration nach erfolgreichem Login
- [ ] Security Audit Log
- [ ] AdminLTE-Sicherheitsübersicht
- [ ] Sichere manuelle Entsperrung
- [ ] E-Mail-Verifikation vorbereitet
- [ ] Adaptive CAPTCHA-Schnittstelle vorbereitet
- [ ] DEV-Abnahme

## V0.2.4 – Integrity

Datenintegrität und Idempotenz:

- [ ] Fachliche UNIQUE-Constraints
- [ ] Normalisierte Vergleichswerte
- [ ] Schutz vor doppeltem Absenden
- [ ] Idempotente Importe
- [ ] Importkonflikt-Anzeige
- [ ] Duplikat-Audit und Reparaturwerkzeug

## V0.2.5 – Atlas Final

- [ ] Vollständiger Installations- und Upgrade-Test
- [ ] Sicherheitsprüfung
- [ ] Datenbankaudit
- [ ] Masterdata-Kompletttest
- [ ] Backup- und Restore-Test
- [ ] Responsive UI-Abnahme
- [ ] Release Candidate
- [ ] Merge nach `main`
- [ ] Produktives Deployment

## Definition of Done

Atlas gilt erst als abgeschlossen, wenn:

- keine bekannten kritischen Fehler offen sind,
- keine PHP-Warnings oder Stacktraces im Browser erscheinen,
- Installation und Upgrade reproduzierbar funktionieren,
- Wartungs- und Recovery-Abläufe getestet sind,
- öffentliche Schreiboperationen gegen automatisierten Missbrauch geschützt sind,
- Logs keine Passwörter, Tokens, Session-Cookies oder vollständige personenbezogene Daten enthalten,
- Datenbank und Anwendung fachliche Duplikate verhindern,
- alle Atlas-Dokumente aktuell sind.
