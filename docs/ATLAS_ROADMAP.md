# Atlas Roadmap

## Ziel

Atlas stellt den stabilen, sicheren und wartbaren Anwendungskern des Wachplaners bereit.
Bis zum Atlas-Final-Release werden keine Leitstellenspiel-Synchronisationsfunktionen
entwickelt.

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
- [ ] Abnahme auf DEV

## V0.2.3 – Shield

Schutz vor automatisiertem Missbrauch:

- [ ] Registrierungssteuerung
- [ ] Honeypot
- [ ] Formular-Mindestzeit
- [ ] Rate Limiting
- [ ] abgestufte Login-Sperren
- [ ] E-Mail-Verifikation
- [ ] Sicherheits-Audit-Log

## V0.2.4 – Integrity

Datenintegrität und Idempotenz:

- [ ] fachliche UNIQUE-Constraints
- [ ] normalisierte Vergleichswerte
- [ ] Schutz vor doppeltem Absenden
- [ ] idempotente Importe
- [ ] Importkonflikt-Anzeige
- [ ] Duplikat-Audit und Reparaturwerkzeug

## V0.2.5 – Atlas Final

- [ ] vollständiger Installations- und Upgrade-Test
- [ ] Sicherheitsprüfung
- [ ] Datenbankaudit
- [ ] Masterdata-Kompletttest
- [ ] Backup- und Restore-Test
- [ ] responsive UI-Abnahme
- [ ] Release Candidate
- [ ] Merge nach `main`
- [ ] produktives Deployment

## Definition of Done

Atlas gilt erst als abgeschlossen, wenn:

- keine bekannten kritischen Fehler offen sind,
- keine PHP-Warnings oder Stacktraces im Browser erscheinen,
- Installation und Upgrade reproduzierbar funktionieren,
- Wartungs- und Recovery-Abläufe getestet sind,
- Logs keine Zugangsdaten oder Tokens enthalten,
- alle Atlas-Dokumente aktuell sind.
