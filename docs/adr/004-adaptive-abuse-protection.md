# ADR 004 – Adaptive Abuse Protection

## Status

Accepted

## Entscheidung

Shield verwendet mehrere leichte Schutzschichten statt eines permanent sichtbaren CAPTCHA.

Reihenfolge:

1. CSRF
2. Honeypot
3. signierte Formularzeit
4. Rate Limiting
5. temporäre Sperre
6. optional später adaptives CAPTCHA

## Begründung

- legitime Benutzer werden nicht unnötig behindert,
- einfache Bots werden früh erkannt,
- stärkere Maßnahmen werden nur bei Auffälligkeiten eingesetzt,
- kein externer CAPTCHA-Anbieter ist für den Grundbetrieb erforderlich.

## Konsequenzen

- Schutzentscheidungen müssen auditierbar sein,
- Grenzwerte benötigen sichere Defaults,
- CAPTCHA bleibt austauschbar und optional.
