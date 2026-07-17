# ADR-001: Mehrstufiger Wartungsmodus

## Status

Accepted

## Entscheidung

Der Wachplaner verwendet drei Wartungsebenen in fester Priorität:

1. ENV-Notfallmodus
2. automatisch erkannter Systemausfall
3. administrativ aktivierte Wartung
4. Normalbetrieb

## Begründung

Die Anwendung muss auch bei nicht erreichbarer Datenbank kontrolliert reagieren.
Datenbankbasierte Einstellungen allein reichen dafür nicht aus.

## Verhalten

### ENV-Notfallmodus

- höchste Priorität
- keine Datenbank erforderlich
- statische 503-Seite
- kein Login

### Automatische Wartung

- Aktivierung bei fehlgeschlagener Datenbankverbindung
- lokaler Status unter `storage/system/maintenance-state.json`
- Circuit Breaker verhindert wiederholte DB-Verbindungen
- automatische erneute Prüfung
- automatische Freigabe nach erfolgreicher Recovery
- System-Logging ohne Zugangsdaten

### Manuelle Wartung

- Steuerung über AdminLTE
- Login bleibt erreichbar
- nur Administratoren erhalten Vollzugriff
- normale Benutzer und Besucher sehen die Wartungsseite
- Registrierung ist immer gesperrt

## Konsequenzen

- `public/index.php` prüft den Betriebszustand vor dem normalen Routing.
- Die statische Wartungsseite darf weder Datenbank noch externe Assets benötigen.
- Eine manuelle Wartung wird niemals automatisch beendet.
