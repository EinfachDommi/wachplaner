# Guardian G2 – System-Center

## Ziel

Das bestehende Guardian-System wird additiv erweitert. Bestehende Namespaces, Masterdata-Komponenten und Routen bleiben kompatibel.

## Enthalten

- System-Center mit Bereichen Übersicht, Health, Wartung, Logs, Updates, Sicherheit und Einstellungen
- zentraler SettingsService auf Basis der bestehenden `system_settings`-Tabelle
- vorbereitete Feature Flags für Hermes, API, Backup und Mail
- Logdatei- und Logauszug-Anzeige ohne Datenbankänderung
- bestehender Wartungsmodus und Health-Checks werden weiterverwendet

## Architekturregel

Bis Atlas Final erfolgen keine großflächigen Verschiebungen bestehender Klassen. Neue Komponenten werden additiv eingeführt und vorhandene Schnittstellen bleiben stabil.

## Datenbank

Keine Migration erforderlich. Die generische Tabelle `system_settings` speichert auch die neuen Einstellungen und Feature Flags.
