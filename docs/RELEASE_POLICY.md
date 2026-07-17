# Release Policy

## Branches

- `main`: Produktion
- `develop`: integrierter Entwicklungsstand
- `feature/*`: neue Entwicklung
- `release/*`: Abnahme und Stabilisierung
- `hotfix/*`: kritische Produktionskorrekturen

## Atlas 0.2.x

V0.2.2 bis spätestens V0.2.5 dienen ausschließlich systemrelevanter
Grundlagenarbeit. Fachliche Leitstellenspiel-Synchronisation beginnt erst nach
Atlas Final.

## Deployment

1. vollständiges Backup
2. Wartungsmodus aktivieren
3. Dateien deployen
4. `/upgrade` ausführen
5. `/system` und `/system/health` prüfen
6. Smoke-Test als Administrator
7. Wartungsmodus deaktivieren
