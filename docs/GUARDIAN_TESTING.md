# Guardian Testing

## Upgrade

- [ ] `.env` wurde nicht überschrieben
- [ ] neue Guardian-Variablen wurden ergänzt
- [ ] `/upgrade` ist vor der Migration erreichbar
- [ ] Migration `20260717_0220_guardian_system_health.sql` läuft einmal
- [ ] zweiter Upgrade-Aufruf zeigt keine offene Guardian-Migration

## Manuelle Wartung

- [ ] Wartung über `/system` aktivieren
- [ ] ausgeloggter Besucher sieht HTTP 503
- [ ] Wartungsseite zeigt keine Registrierung
- [ ] Login bleibt erreichbar
- [ ] Admin erhält Vollzugriff und Warnbanner
- [ ] normaler Benutzer erhält keinen Vollzugriff
- [ ] Registrierung ist während Wartung gesperrt
- [ ] Wartung durch Admin deaktivieren

## Automatischer DB-Failover

Nur auf DEV testen:

1. Datenbankzugang vorübergehend ungültig machen oder DB-Verbindung sperren.
2. Anwendung aufrufen.
3. Statische 503-Seite ohne Stacktrace erwarten.
4. `storage/logs/system.log` auf `SYS-DB-001` prüfen.
5. Mehrere Requests innerhalb von 30 Sekunden ausführen.
6. Prüfen, dass der Circuit Breaker keine neue DB-Verbindung pro Request erzwingt.
7. Datenbank wiederherstellen.
8. Nach Ablauf des Retry-Intervalls erneut aufrufen.
9. Normalbetrieb und `SYS-DB-RECOVERED` prüfen.

## ENV-Notfallmodus

- [ ] `MAINTENANCE_FORCE=true`
- [ ] statische 503-Seite ohne Datenbankzugriff
- [ ] kein Login-Link
- [ ] anschließend zwingend wieder `false`

## Health Endpoint

- [ ] Normalbetrieb: HTTP 200 und `status=ok`
- [ ] manuelle Wartung: HTTP 503
- [ ] automatischer Ausfall: HTTP 503
- [ ] Antwort enthält keine Zugangsdaten oder Serverpfade

## Sicherheit

- [ ] Session-Cookie ist `HttpOnly`
- [ ] Session-Cookie ist unter HTTPS `Secure`
- [ ] Registrierung ist im Wartungsmodus nicht erreichbar
- [ ] Wartungsseite verwendet keine externen Assets
- [ ] Logs enthalten keine DB-Passwörter, Cookies oder CSRF-Tokens
