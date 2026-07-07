# Datenbank

## Bestehende Kernbereiche

- Benutzer und Authentifizierung
- Projekte
- Stammdaten
- Dashboard-Grunddaten

## Stammdaten

### Fahrzeugtypen
Quelle: `LeitstellenspielFahrzeugtypen.xlsx`

Enthält Fahrzeugname, Kategorie, benötigte Wache, Erweiterung, Personal, Ausbildung und Credits.

### Ausbildungen
Quelle: `LEitstellenspielAsubildungen.xlsx`

Ausbildungen werden perspektivisch normalisiert:
- `training_types`
- `training_vehicle_type`

### Erweiterungen
Quelle: `LeitstellenspielErweiterungen.xlsx`

Dient später für Kostenplanung und Soll-/Ist-Abgleich.

### Baukosten
Quelle: `Wachenbaukosten bis 10k.xlsx`

Wichtig: Grundwachen unterscheiden normale und kleine Varianten:
- Feuerwache
- Feuerwache (Kleinwache)
- Rettungswache
- Rettungswache (Kleinwache)
- Polizeiwache
- Polizeiwache (Kleinwache)

## Geplante Tabellen ab 0.2+

- `lss_settings`
- `lss_sessions`
- `lss_sync_logs`
- `lss_buildings`
- `lss_vehicles`
- `lss_personnel`
- `planned_stations`
- `planned_station_vehicles`
- `planned_station_extensions`
- `planned_training_requirements`
