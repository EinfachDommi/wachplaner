# Commit-Vorschläge

## V0.2.1 – Atlas Hotfix

### Summary
fix(core): stabilize Atlas configuration and upgrade flow

### Description
Sprint: Atlas Hotfix / V0.2.1

Fixed:
- Added EnvLoader for root .env files
- Added central Config class
- Added central ErrorHandler with readable error pages
- Hardened Database connection handling
- Prevented PDO fatal errors when .env is missing or incomplete
- Kept /install and /upgrade routing before database bootstrapping
- Added app version/build metadata migration

Changed:
- Expanded .env.example as canonical environment reference
- Updated system status with environment, build and .env checks

Target branch: develop
