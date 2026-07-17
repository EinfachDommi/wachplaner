# System Overview

## Guardian Request Flow

```text
Request
  |
  v
Bootstrap + Config
  |
  +-- MAINTENANCE_FORCE? ---- yes --> Static 503
  |
  +-- Circuit breaker active? yes --> Static 503
  |
  v
Database connection + SELECT 1
  |
  +-- failed --> automatic maintenance state + system.log + Static 503
  |
  +-- restored --> clear automatic state + recovery log
  |
  v
Read manual maintenance settings
  |
  +-- active and not admin --> Maintenance page
  |
  v
Normal routing
```

## Storage

`storage/system/maintenance-state.json` enthält ausschließlich nicht sensible
Betriebsinformationen. Zugangsdaten, Stacktraces und Sessiondaten dürfen dort
nicht gespeichert werden.

## Health Endpoint

`GET /system/health`

- HTTP 200 bei betriebsbereitem System
- HTTP 503 bei automatischer oder erzwungener Wartung
- keine vertraulichen Konfigurationswerte
