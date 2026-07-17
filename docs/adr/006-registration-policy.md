# ADR 006 – Registration Policy

## Status

Accepted

## Entscheidung

Die Registrierung unterstützt die Zustände `disabled`, `open` und `invite_only`.
Aktive Wartung erzwingt unabhängig davon den Zustand `disabled`.

## Begründung

Der Betreiber muss öffentliche Registrierung vollständig sperren oder kontrolliert über
Einladungen öffnen können. Wartungs- und Sicherheitszustände haben Vorrang vor Komfort.

## Konsequenzen

- `/register` prüft die zentrale Richtlinie vor Darstellung und Verarbeitung,
- die Sidebar und Login-Seite zeigen Registrierung nur bei erlaubtem Zustand,
- Einladungen werden ausschließlich gehasht gespeichert,
- Änderungen werden auditiert.
