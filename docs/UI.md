# Benutzeroberfläche

## Grundlage

Die Oberfläche basiert ab Build `20260711.003` auf:

- Bootstrap 5.3
- AdminLTE 4
- Bootstrap Icons
- Vanilla JavaScript

Die Bibliotheken werden derzeit über jsDelivr geladen. Für einen späteren produktiven Offline-Betrieb können die Assets lokal gebündelt werden.

## Layouts

- `app/Views/layouts/app/`: geschützte Anwendungsseiten
- `app/Views/layouts/auth/`: Login und Registrierung
- `app/Views/partials/`: Navbar und Sidebar

## Navigation

Die Sidebar ist dauerhaft in folgende Fachbereiche gegliedert:

1. Dashboard
2. Planung
3. Stammdaten
4. Leitstellenspiel
5. Administration

Noch nicht implementierte Hermes- und Planungsmodule werden sichtbar, aber deaktiviert dargestellt.

## Statusfarben

- Blau: primäre Aktion oder Information
- Grün: erfolgreich oder vollständig
- Gelb: Warnung oder ausstehend
- Rot: Fehler oder kritischer Zustand
- Grau: deaktiviert oder noch nicht verfügbar

## Dark Mode

Der Benutzer kann das Farbschema über die Navbar wechseln. Die Auswahl wird unter `wachplaner-theme` in `localStorage` gespeichert.
