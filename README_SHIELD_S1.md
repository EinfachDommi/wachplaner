# Shield S1 – Security Core

## Installation

1. Patch pfadtreu in den aktuellen `develop`-Stand kopieren.
2. `.env` bleibt unverändert.
3. `/upgrade` ausführen.
4. Migration `20260717_0400_shield_security_core.sql` prüfen.
5. `/system` und `/system/health` testen.

## Wichtig

Login und Registrierung werden durch S1 noch nicht verändert.
Die neuen Komponenten bilden ausschließlich die Grundlage für Shield S2–S4.
