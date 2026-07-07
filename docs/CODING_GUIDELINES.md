# Coding Guidelines

Der Wachplaner wird ab V0.2 nach einer klaren, wartbaren Struktur weiterentwickelt.

## PHP

- Zielstandard: PSR-12.
- Neue fachliche Logik gehört in Services, nicht direkt in Views oder Routen.
- Datenbankzugriffe erfolgen ausschließlich über PDO und Prepared Statements.
- Ausgaben in Views werden mit `e()` escaped.
- Formulare verwenden CSRF-Token.

## Commits

Empfohlenes Format:

```text
feat(scope): short description
fix(scope): short description
docs(scope): short description
refactor(scope): short description
```

Beispiele:

```text
feat(masterdata): add importer foundation
fix(auth): validate empty login credentials
docs(api): describe lss session workflow
```

## Branches

- `main`: stabile Releases
- `develop`: laufende Entwicklung
- `release/*`: Release-Vorbereitung
- `feature/*`: einzelne Features
- `fix/*`: Fehlerbehebungen

## Keine Quick Fixes

Fehler werden an der Ursache behoben. Workarounds werden nur dokumentiert eingesetzt, wenn es keine saubere Sofortlösung gibt.
