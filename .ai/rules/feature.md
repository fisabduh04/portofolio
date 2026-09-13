---
paths:
  - tests/Feature/WaliKelasTest.php
---

# Feature

## Opt-in MySQL tests use isolated temporary schemas
Run WaliKelasTest with WALIKELAS_TEST_MYSQL=1 to test MySQL. Each test creates and removes only its own random walikelas_test_* schema; never migrate or refresh the application's database. When cloning getConfig(), replace the connection name as well as database and URL: Migrator switches connections using getName(). Check actual database and Schema connection before migrating. The default test mode remains SQLite in memory.
