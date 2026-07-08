# Commit-Vorschläge

## V0.2.1 – Atlas Hotfix Patch 1

### Summary

```text
fix(upgrade): remove transaction handling for SQL migrations
```

### Description

```text
Fixed:
- Prevented "There is no active transaction" during /upgrade
- SQL upgrade migrations now run without explicit PDO transactions
- Added explanation for MySQL/MariaDB implicit DDL commits

Target branch: develop
```

## V0.2.1 – Atlas Quality Layer

### Summary

```text
fix(core): harden Atlas quality layer
```

### Description

```text
Sprint: Atlas Hotfix / V0.2.1

Fixed:
- DB_PORT is now respected by the database connection
- Error pages now include a traceable error ID
- Upgrade runs are logged to storage/logs/upgrade.log

Improved:
- Added Config validation helpers
- Added SystemCheckService
- Added multi-channel Logger support
- Updated storage/version.json after successful upgrades

Documentation:
- Added docs/QUALITY.md
- Added docs/KNOWN_BUGS.md
- Updated architecture decisions

Target branch: develop
```
