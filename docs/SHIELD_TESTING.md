# Shield Testing

## Registrierung

- Registrierung global deaktiviert
- Registrierung offen
- Registrierung im Wartungsmodus
- Registrierung mit gültiger Einladung
- Registrierung mit ungültiger oder abgelaufener Einladung
- Honeypot leer
- Honeypot gefüllt
- Formular zu schnell abgesendet
- Formular nach zulässiger Zeit abgesendet
- Mehrfachversuche aus derselben IP
- Mehrfachversuche mit derselben E-Mail
- Fehlermeldung verrät kein bestehendes Konto

## Login

- korrekte Zugangsdaten
- falsches Passwort
- unbekannter Benutzer
- identische Fehlermeldung für falsches Passwort und unbekannten Benutzer
- Session-ID wird nach erfolgreichem Login erneuert
- 5 Fehlversuche
- 10 Fehlversuche
- temporäre Sperre läuft ab
- wiederholte Sperren verlängern die Wartezeit
- Admin kann Sperre lösen
- erfolgreicher Login erzeugt Audit Event
- fehlgeschlagener Login erzeugt Audit Event

## Rate Limiting

- getrennte Scopes für Login und Registrierung
- unterschiedliche Benutzer an derselben IP
- derselbe Benutzer von verschiedenen IPs
- geteilte NAT-IP führt nicht sofort zu permanenter Sperre
- `blocked_until` wird eingehalten
- parallele Requests überschreiten Limits nicht unkontrolliert
- Cleanup alter Fenster
- Datenbankfehler erzeugt kontrollierte Antwort

## Audit Log

- keine Passwörter
- keine CSRF-Token
- keine Session-Cookies
- keine vollständigen Einladungstoken
- Request-ID vorhanden
- Zeitstempel korrekt
- Benutzer-ID nur bei sicherer Zuordnung
- Metadaten begrenzt und maskiert
- nur Administratoren sehen Security Logs

## UI

- System-Center zeigt Registrierungsmodus
- aktive Sperren werden angezeigt
- Entsperraktion besitzt CSRF-Schutz
- Security-Tabelle ist mobil scrollbar
- leere Zustände werden verständlich angezeigt
- keine PHP-Warnings
- keine Stacktraces

## Abnahme

Shield gilt erst als DEV-abgenommen, wenn alle kritischen Fälle bestanden sind und in
`app.log`, `system.log` und Security Audit Log keine sensiblen Werte erscheinen.
