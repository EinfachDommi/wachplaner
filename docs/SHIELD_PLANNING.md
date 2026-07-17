# Shield Planning – V0.2.3

## Mission

Shield schützt Registrierung, Login und weitere öffentliche Schreiboperationen vor
automatisiertem Missbrauch, Brute Force, Replay und Benutzer-Erkennung, ohne legitime
Benutzer unnötig zu behindern.

## Umfang

### Enthalten

- Steuerbare Registrierung
- Optionaler Einladungsmodus
- Honeypot
- Signierte Formularzeit
- Rate Limiting
- Abgestufte Login-Sperren
- Security Audit Log
- Sichere Session-Erneuerung
- AdminLTE-Sicherheitsübersicht
- Vorbereitung für E-Mail-Verifikation
- Vorbereitung für adaptives CAPTCHA

### Nicht enthalten

- Fachliche Duplikatbereinigung
- Idempotency Keys für alle Fachformulare
- UNIQUE-Constraint-Audit des gesamten Datenmodells
- Leitstellenspiel-API
- Externe Single-Sign-On-Anbieter

Diese Punkte gehören zu Integrity oder späteren Versionen.

## Schutzschichten

```text
Request
  ↓
Request-ID
  ↓
Wartungs- und Registrierungsrichtlinie
  ↓
CSRF
  ↓
Honeypot
  ↓
Signierte Formularzeit
  ↓
Rate Limit
  ↓
Eingabevalidierung
  ↓
Authentifizierungs- oder Registrierungsservice
  ↓
Audit Event
  ↓
Response
```

## Registrierungsmodi

- `disabled`: Keine öffentliche Registrierung
- `open`: Öffentliche Registrierung mit Shield-Schutz
- `invite_only`: Registrierung nur mit gültiger Einladung
- `maintenance`: Automatisch erzwungen; keine Registrierung

Die Reihenfolge der Priorität ist:

```text
Wartungsmodus
  > globale Registrierungseinstellung
  > Einladungsrichtlinie
  > normale Registrierung
```

## Rate-Limit-Profile

### Login

- 5 Versuche innerhalb von 5 Minuten: Warnstufe
- 10 Versuche innerhalb von 15 Minuten: temporäre Sperre
- Wiederholte Sperren verlängern die Wartezeit
- Schlüssel aus IP-Präfix und normalisierter Benutzerkennung
- Keine Speicherung des Klartext-Passworts

### Registrierung

- 3 Versuche pro Stunde je IP-Präfix
- 5 Versuche pro Tag je normalisierter E-Mail-Adresse
- Honeypot-Treffer wird als automatisierter Missbrauch protokolliert
- Bei auffälligem Verhalten kann später CAPTCHA verlangt werden

### Passwortbezogene Aktionen

- Eigenes, strengeres Profil
- Keine Wiederverwendung von Login-Zählern
- Keine Offenlegung, ob eine E-Mail existiert

Die Grenzwerte werden über `system_settings` konfigurierbar, erhalten aber sichere Defaults.

## Abgestufte Reaktion

1. Anfrage zulassen
2. Verzögerung oder Warnstatus intern markieren
3. Temporär blockieren
4. Längere Sperre nach wiederholtem Missbrauch
5. Administrative Prüfung bei anhaltenden Auffälligkeiten

Keine permanente automatische IP-Sperre in Shield.

## Datenschutz

Gespeichert werden nur Daten, die für Schutz und Diagnose erforderlich sind:

- Ereignistyp
- Ergebnis
- Zeitpunkt
- gehashter oder gekürzter Netzwerkbezug
- normalisierte Kennung nur gehasht
- Request-ID
- optional Benutzer-ID nach erfolgreicher Zuordnung
- Ablaufzeit einer Sperre

Nicht gespeichert werden:

- Passwörter
- CSRF-Token
- Session-Cookies
- vollständige Authorization-Header
- vollständige Einladungs- oder Verifikationstoken
- unnötige Formularinhalte

## Vorgesehenes Datenmodell

### `security_rate_limits`

- `id`
- `scope`
- `subject_hash`
- `attempt_count`
- `window_started_at`
- `blocked_until`
- `last_attempt_at`
- `created_at`
- `updated_at`

Eindeutiger Schlüssel:

```text
(scope, subject_hash)
```

### `security_audit_logs`

- `id`
- `event_type`
- `severity`
- `result`
- `request_id`
- `user_id`
- `subject_hash`
- `ip_prefix_hash`
- `metadata_json`
- `created_at`

### `registration_invites` (Vorbereitung)

- `id`
- `token_hash`
- `email_normalized`
- `expires_at`
- `used_at`
- `created_by`
- `created_at`

Token werden ausschließlich gehasht gespeichert.

## Services

```text
app/Core/Security/
├── Audit/
│   ├── SecurityAuditLogger.php
│   └── SecurityEvent.php
├── RateLimit/
│   ├── RateLimiter.php
│   ├── RateLimitDecision.php
│   └── RateLimitRepository.php
├── Forms/
│   ├── HoneypotGuard.php
│   └── FormTimingGuard.php
├── Registration/
│   └── RegistrationPolicy.php
└── Request/
    └── RequestId.php
```

Die Struktur wird additiv eingeführt. Bestehende Auth-Klassen werden nicht verschoben.

## AdminLTE-System-Center

Unter `System → Sicherheit`:

- Registrierung: aus / offen / nur Einladung
- Login-Rate-Limit-Status
- Registrierungs-Rate-Limit-Status
- Aktive temporäre Sperren
- Letzte sicherheitsrelevante Ereignisse
- Honeypot-Treffer
- Fehlgeschlagene Loginversuche
- Sichere Entsperraktion
- Vorbereitung für E-Mail-Verifikation und CAPTCHA

## Fehlermeldungen

Login:

```text
Anmeldung nicht möglich. Bitte Zugangsdaten prüfen oder später erneut versuchen.
```

Registrierung:

```text
Die Registrierung konnte nicht abgeschlossen werden.
Bitte Eingaben prüfen oder später erneut versuchen.
```

Rate Limit:

```text
Zu viele Versuche. Bitte warte einen Moment und versuche es später erneut.
```

Keine Meldung bestätigt die Existenz eines Kontos.

## Migrationsplan

Shield erhält genau eine initiale Migration und bei Bedarf kleine Folge-Migrationen.
Die Migration wird erst mit der Implementierung erstellt, nicht im Planning-Commit.

Geplant:

- Security-Tabellen
- sichere Indexe
- Standardwerte in `system_settings`
- keine Änderung bestehender Benutzerpasswort-Hashes
- keine Löschung bestehender Daten

## Rollout

1. Services und Migration hinzufügen
2. Audit-Logging zunächst passiv aktivieren
3. Rate Limiting auf DEV mit großzügigen Grenzwerten testen
4. Honeypot und Formularzeit aktivieren
5. Registrierungsschalter integrieren
6. AdminLTE-Sicherheitsseite vervollständigen
7. Fehlerszenarien testen
8. Grenzwerte finalisieren
9. Shield DEV-Abnahme

## Definition of Done

Shield ist abgeschlossen, wenn:

- Bots einfache Registrierungsformulare nicht ungehindert automatisieren können,
- Login- und Registrierungsversuche rate-limitiert sind,
- Fehlermeldungen keine Benutzerkonten offenlegen,
- Session-ID nach Login erneuert wird,
- Wartungsmodus Registrierung zuverlässig sperrt,
- Sicherheitsereignisse datensparsam protokolliert werden,
- Admins aktive Sperren nachvollziehen und sicher lösen können,
- keine Zugangsdaten oder Tokens in Logs erscheinen,
- alle Tests aus `SHIELD_TESTING.md` erfolgreich sind.
