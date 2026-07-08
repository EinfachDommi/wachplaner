# Known Bugs

| ID | Status | Priorität | Beschreibung | Notiz |
|---|---|---|---|---|
| BUG-0001 | behoben | hoch | `/upgrade` brach mit `There is no active transaction` ab | DDL-Migrationen laufen ohne explizite PDO-Transaktion. |
| BUG-0002 | behoben | hoch | Fehlende/abweichende `.env` führte zu PDO-Fatal mit root ohne Passwort | Config-/Env-Validierung und ErrorHandler ergänzt. |

Neue Fehler werden hier dokumentiert, wenn sie nicht sofort im selben Arbeitsschritt behoben werden.
