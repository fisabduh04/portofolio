---
paths:
  - 'database/migrations/**'
---

# Migrations

## Preserve student and employee history on parent deletion
Use restrictive foreign keys from student attendance/class placements and employee attendance, scan logs, leave, schedules, rule allocations and event participation to their person record. Preserve existing restrictive wali kelas and logbook links. Deactivate people with history rather than cascading deletion. Student bulk deletion must be atomic, and photos are removed only after database deletion commits.

## Preserve accounts when enforcing employee references
users.pegawai_id is nullable and restricts employee deletion. Repair legacy orphan references to NULL without guessing a replacement employee or changing account credentials/role/status; record user ID and previous employee ID in the application log. Rollback removes the constraint but must not recreate invalid references.
