# Threat Model

## Schutzobjekte

- Benutzerkonten
- Administratorzugänge
- Sessions
- Registrierungsprozess
- Systemkonfiguration
- Audit- und Systemlogs
- Verfügbarkeit der Anwendung

## Angreifermodelle

### Automatisierter Bot

Ziele:

- Massenregistrierungen
- Formularspam
- Credential Stuffing
- Erkennung existierender Benutzer
- Erzeugung unnötiger Datenbanklast

### Unauthentifizierter Angreifer

Ziele:

- Brute Force
- Session-Manipulation
- Replay von Formularanfragen
- Missbrauch von Passwort- oder Registrierungsfunktionen

### Authentifizierter Benutzer

Ziele:

- Umgehen von Rollen
- Erzeugen doppelter oder missbräuchlicher Einträge
- Einsicht in fremde Sicherheitsdaten
- Manipulation von Systemeinstellungen

### Infrastrukturfehler

Risiken:

- Datenbankausfall
- nicht beschreibbarer Storage
- fehlende Migration
- fehlerhafte Konfiguration

Guardian behandelt Infrastrukturfehler; Shield verarbeitet Missbrauch an öffentlichen
Authentifizierungs- und Registrierungsendpunkten.

## Zentrale Risiken und Kontrollen

| Risiko | Kontrolle |
|---|---|
| Massenregistrierung | Registrierungsschalter, Honeypot, Formularzeit, Rate Limit |
| Brute Force | IP- und Identitäts-basiertes Rate Limit, temporäre Sperren |
| Benutzer-Erkennung | Generische Fehlermeldungen und gleichartige Responses |
| Session Fixation | Session-Regeneration nach erfolgreichem Login |
| CSRF | CSRF-Token auf jeder Schreiboperation |
| Replay/Doppelklick | In Shield teilweise erkannt; vollständige Idempotenz in Integrity |
| Log-Leak | Redaction, Datenminimierung, keine Secrets |
| DoS durch Rate-Limit-Tabelle | Indexe, begrenzte Metadaten, Cleanup-Strategie |
| Falsche Sperrung geteilter IPs | Kombination aus IP-Präfix und Identitäts-Hash |
| Proxy-Header-Spoofing | Nur vertrauenswürdige Proxy-Konfiguration auswerten |

## Vertrauensgrenzen

- Browser ↔ Webserver
- Webserver ↔ PHP-Anwendung
- Anwendung ↔ Datenbank
- Anwendung ↔ lokaler Storage
- später: Anwendung ↔ Mail-/CAPTCHA-Dienst

## Nicht-Ziele von Shield

- Vollständiger Schutz gegen verteilte DDoS-Angriffe
- Web Application Firewall
- Malware-Erkennung
- externe Identitätsanbieter
- vollständige fachliche Duplikatbereinigung
