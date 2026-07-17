# Wachplaner – Projektstatus

## Aktueller Stand

- Produktiv: V0.1.2 auf `main`
- Entwicklung: V0.2.2-dev Guardian auf `develop`
- Entwicklungsserver: `https://dev.wachplaner.sh-com.de`
- nächster Meilenstein: V0.2.3 Shield

## Guardian abgeschlossen bzw. integriert

- geplanter Wartungsmodus über das AdminLTE-System-Center
- Registrierungssperre während Wartung
- Adminzugriff während geplanter Wartung
- automatischer DB-Failover mit HTTP 503
- Circuit Breaker und Recovery-Erkennung
- lokales System-Logging
- modulares Health-Check-System
- System-Center mit Health, Wartung, Logs, Updates, Sicherheit und Einstellungen
- vorbereitete Feature Flags
- stabilisierter Masterdata- und XLSX-Import

## Atlas-Roadmap

- V0.2.2 Guardian: Systembetrieb und Wartung
- V0.2.3 Shield: Anti-Spam und Authentifizierungsschutz
- V0.2.4 Integrity: Datenintegrität und Duplikatschutz
- V0.2.5 Atlas Final: Release Candidate und Produktivdeployment

## Entwicklungsregel

Bis Atlas Final werden systemrelevante Funktionen additiv und rückwärtskompatibel ergänzt. Bestehende, stabile Klassen oder Namespaces werden nicht ohne zwingenden Grund verschoben.
