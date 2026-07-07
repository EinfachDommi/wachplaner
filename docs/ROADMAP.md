# Wachplaner Roadmap

## Stable Base

### 0.1.2
Status: stabil.

Enthält:
- MVC-Grundstruktur
- Login/Register
- Installer
- `.htaccess`-Routing auf `public/`
- Stammdaten für Fahrzeugtypen, Ausbildungen, Erweiterungen und Baukosten
- Projektverwaltung und Dashboard-Grundlage

## 0.2.0 – Leitstellenspiel Sync-Grundlage

Ziel: sichere und sparsame Datenübernahme aus Leitstellenspiel.de.

Geplant:
- LSS-Login mit Loginname/E-Mail und Passwort
- Cookie-Session-Verwaltung
- automatischer Re-Login bei abgelaufener Session
- statischer JSON-Cache unter `storage/cache/lss/`
- Sync-Logs in Datenbank und Datei
- manuelle Synchronisation im Adminbereich
- Vorbereitung für Cronjob-Sync

## 0.3.0 – Realbau-Dashboard

Geplant:
- Projektfortschritt in Prozent
- Status-Ampeln für Wachen, Fahrzeuge, Erweiterungen und Ausbildungen
- Filter und Suche
- Projektbezogene Soll-Ansicht

## 0.4.0 – Realbau-Planung

Geplant:
- Projekt → Leitstelle → Wache → Sollbestand
- Soll-Fahrzeuge je Wache
- Soll-Erweiterungen je Wache
- Kostenberechnung auf Basis der Stammdaten

## 0.5.0 – Kostenplanung

Geplant:
- Baukosten abhängig von aktueller Wachenanzahl
- Fahrzeugkosten
- Erweiterungskosten
- Projekt-Gesamtkosten

## 0.6.0 – Ausbildungsplanung

Geplant:
- Ausbildungsbedarf aus Fahrzeug-Sollbestand ableiten
- Fehlende Ausbildungen je Projekt/Wache anzeigen

## 0.7.0 – Soll-/Ist-Abgleich

Geplant:
- API-Daten gegen Planung prüfen
- Status: vorhanden, im Bau, fehlt
- automatische To-do-Liste
