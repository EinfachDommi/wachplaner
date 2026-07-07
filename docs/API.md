# Leitstellenspiel-Integration

## Ziel

Die Anwendung synchronisiert den realen Spielstand aus Leitstellenspiel.de mit der lokalen MySQL-Datenbank.

## Login-Methode

Verwendet wird Loginname/E-Mail und Passwort.

Geplanter Ablauf:
1. Login-Seite abrufen
2. CSRF-/Authentifizierungsdaten auslesen, falls erforderlich
3. Login absenden
4. Cookie-Session speichern
5. Session bei Folgeabrufen wiederverwenden
6. Bei Ablauf automatisch neu anmelden

## Caching

API-Antworten werden statisch gespeichert:

```text
storage/cache/lss/
├── buildings.json
├── vehicles.json
├── personnel.json
└── meta.json
```

Das Dashboard greift nicht direkt auf die API zu.

## Sync-Strategie

- API abrufen
- JSON-Cache schreiben
- MySQL aktualisieren
- Sync-Log schreiben
- Dashboard nutzt MySQL

## Anfrage-Reduktion

Standardziel: API nur bei manueller Synchronisation oder nach Ablauf des Cache-Zeitfensters abfragen.
