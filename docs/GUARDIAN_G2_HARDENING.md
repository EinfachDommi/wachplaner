# Guardian G2 Hardening

Version: `0.2.2-dev`  
Build: `20260717.003`  
Branch: `develop`

## Änderungen

- Datenbankbasierte Zeitzone wird nach erfolgreichem Bootstrap angewendet.
- Fehlende optionale Zähltabellen brechen das System-Center nicht mehr ab.
- Health-Zusammenfassung enthält gesunde, warnende und kritische Prüfungen.
- Logzeilen werden auf 2.000 Zeichen begrenzt und sensible Schlüssel maskiert.
- Logeinträge erhalten eine erkannte Schweregradanzeige.
- Speichermeldungen unterscheiden Wartung, Systemeinstellungen und Feature Flags.
- Bestehende Routen, Namespaces und Datenbanktabellen bleiben unverändert.

## Abnahmetest

1. Unter **System → Einstellungen** eine andere Zeitzone speichern und Seite neu laden.
2. Wartungsmodus aktivieren und wieder deaktivieren.
3. Feature Flag aktivieren und wieder deaktivieren.
4. Unter **Logs** Schweregrade und maskierte sensible Werte kontrollieren.
5. `/system/health` aufrufen.
6. Prüfen, dass eine fehlende optionale Stammdatentabelle nur einen leeren Zähler verursacht und keine Fehlerseite.
