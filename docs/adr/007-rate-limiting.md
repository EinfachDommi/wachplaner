# ADR 007 – Rate Limiting

## Status

Accepted

## Entscheidung

Rate Limits werden in der Datenbank pro Scope und gehashtem Subjekt gespeichert.
Login und Registrierung verwenden getrennte Profile.

## Schlüsselbildung

Der Schlüssel kombiniert je nach Scope:

- gekürzten/gehashten Netzwerkbezug,
- gehashte normalisierte Benutzerkennung oder E-Mail,
- den Aktions-Scope.

## Begründung

Eine reine IP-Sperre benachteiligt Benutzer hinter NAT. Eine reine Kennungssperre lässt
verteilte Angriffe zu. Die Kombination erlaubt abgestufte Entscheidungen.

## Konsequenzen

- keine Klartextkennungen in der Rate-Limit-Tabelle,
- Index auf `(scope, subject_hash)`,
- zeitlich begrenzte Sperren,
- Cleanup alter Einträge,
- keine permanente automatische Sperre in Shield.
