---
paths:
  - tests/Feature/WaliKelasTest.php
---

# Feature

## Opt-in MySQL tests use isolated temporary schemas
Run WaliKelasTest with WALIKELAS_TEST_MYSQL=1 to test MySQL. Each test creates and removes only its own random walikelas_test_* schema; never migrate or refresh the application's database. When cloning getConfig(), replace the connection name as well as database and URL: Migrator switches connections using getName(). Check actual database and Schema connection before migrating. The default test mode remains SQLite in memory.

## Opt-in MySQL tests use isolated temporary schemas
Superseded by the user's MySQL-only testing decision: application tests now use MySQL by default through Tests\TestCase, with a new random portofolio_test_* schema per test. No WALIKELAS_TEST_MYSQL flag or SQLite fallback. Never migrate or refresh the application's database. Clone connection credentials while replacing connection name, database and URL, and verify both DB and Schema connections before migrations.
