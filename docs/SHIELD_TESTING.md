# Shield Testing

## S2 Rate Limiting

### Migration

- Tabelle `security_rate_limits` wird angelegt
- UNIQUE-Key auf `(scope, subject_hash)` ist vorhanden
- Guardian-Einstellungen bleiben erhalten
- Shield-Defaults stehen in `system_settings`
- wiederholtes Upgrade erzeugt keine Duplikate

### RateLimiter

- erster Versuch erlaubt
- Restversuche werden korrekt reduziert
- Fensterablauf setzt Zähler zurück
- Überschreitung erzeugt temporäre Sperre
- aktive Sperre liefert `retryAfterSeconds`
- erneute Überschreitung erhöht die Sperrdauer
- maximale Sperrdauer wird eingehalten
- erfolgreicher Clear entfernt den Datensatz
- Cleanup löscht nur abgelaufene alte Einträge
- parallele Zugriffe erzeugen keinen doppelten Datensatz

### Datenschutz

- `subject_hash` enthält keine lesbare E-Mail oder IP
- keine Passwörter oder Tokens werden gespeichert
- Admin-Auswertung zeigt keine Subjekt-Hashes in öffentlichen Ansichten

### Kompatibilität

- bestehender Login funktioniert unverändert
- bestehende Registrierung funktioniert unverändert
- `/system` funktioniert
- `/system/health` funktioniert
- keine PHP-Warnings oder Stacktraces
