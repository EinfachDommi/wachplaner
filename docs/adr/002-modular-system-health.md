# ADR-002 – Modularer System-Health-Core

## Status

Accepted

## Kontext

Systemprüfungen waren zuvor in einer einzelnen Serviceklasse und teilweise im Front Controller verteilt. Neue Prüfungen hätten die Klasse weiter vergrößert und den Bootstrap enger mit einzelnen Infrastrukturkomponenten gekoppelt.

## Entscheidung

Jede Systemprüfung implementiert `SystemCheckInterface` und liefert ein unveränderliches `CheckResult`. `HealthCheck` führt beliebig viele Prüfungen aus und erstellt einen aggregierten Bericht.

Die bestehende `SystemCheckService` bleibt als kompatibler Adapter für die AdminLTE-Ansicht erhalten.

## Konsequenzen

- Prüfungen können unabhängig erweitert und getestet werden.
- Kritische und nichtkritische Zustände werden unterschieden.
- JSON-, CLI- und Admin-Ausgaben können dieselben Resultate verwenden.
- Bootstrap-Checks bleiben bewusst klein und datenbankunabhängig.
