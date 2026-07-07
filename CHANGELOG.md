# Changelog

## V0.1
- Neue Realbau-Codebasis erstellt
- MySQL-Schema mit Stammdaten erstellt
- Excel-Stammdaten direkt in SQL übernommen
- Login/Register eingebaut
- Dashboard und Projektverwaltung ergänzt

## V0.1.1
- Fehler bei Admin-Registrierung behoben: SQL-String für Rolle `admin` korrigiert.
- Fehler bei Projekterstellung behoben: SQL-String für Status `active` korrigiert.
- Bootstrap auf `require_once` umgestellt, damit `/install` robuster funktioniert.


## V0.1.2

- Root-`.htaccess` ergänzt, damit alle Requests automatisch über `public/` laufen.
- README um Public-Root-/DocumentRoot-Hinweise erweitert.
