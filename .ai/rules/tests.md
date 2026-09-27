---
paths:
  - 'tests/**'
---

# Tests

## Application tests use isolated MySQL schemas
Run php artisan test --compact with MySQL; no SQLite extension or opt-in flags are required. Tests\TestCase creates a random portofolio_test_* database per test and drops only that database afterward, using the local MySQL credentials (CREATE/DROP DATABASE privilege required). Never run migrations or cleanup on school data. The standalone face prototype has its own test base and SQLite-only runner; exclude it from the main PHPUnit suite.
