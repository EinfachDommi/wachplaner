# Architektur

Der Wachplaner ist eine PHP/MySQL-Anwendung für Realbau-Projekte in Leitstellenspiel.de.

## Grundprinzipien

- `public/` ist der einzige direkt erreichbare Webroot.
- Anwendungscode liegt außerhalb von `public/`.
- Datenbankzugriff erfolgt zentral über PDO.
- Stammdaten werden versioniert und importierbar gehalten.
- Leitstellenspiel-Daten werden nie direkt im Dashboard abgefragt, sondern über Cache und MySQL bereitgestellt.

## Zielstruktur

```text
app/
├── Core/
├── Controllers/
├── Models/
├── Services/
│   ├── LSS/
│   ├── Cache/
│   ├── Planning/
│   └── Calculation/
├── Middleware/
└── Views/
```

## Service Layer

### LSS
Verantwortlich für Login, Cookie-Session, API-Abrufe und Re-Login.

### Cache
Verantwortlich für statische JSON-Dateien, Ablaufzeiten und Cache-Fallback.

### Planning
Verantwortlich für Realbau-Projekte, Soll-Wachen, Soll-Fahrzeuge und Soll-Erweiterungen.

### Calculation
Verantwortlich für Baukosten, Fahrzeugkosten, Erweiterungskosten und Ausbildungsbedarf.
