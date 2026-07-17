# ADR-003 – Additives System-Center

- **Status:** Accepted
- **Version:** 0.2.2 Guardian

## Entscheidung

Das System-Center wird ohne Big-Bang-Refactoring ergänzt. Bestehende Klassen werden nicht verschoben und ihre Namespaces bleiben unverändert.

## Begründung

Atlas priorisiert Stabilität. Eine vollständige Modultrennung würde unnötige Integrationsrisiken erzeugen. Neue Systemkomponenten dürfen bereits der Zielarchitektur folgen, während funktionierender Altcode kompatibel bleibt.
