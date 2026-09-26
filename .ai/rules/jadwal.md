---
paths:
  - 'resources/views/jadwal/**'
---

# Jadwal

## Jadwal active periods and table state
On jadwal.index, an empty or all year filter means every currently active academic period, never the first period or inactive history. Conflict detection must stay within each period across that active set. CRUD/import redirects must preserve the originating table filters, search, sort, page size, and pagination.

## Restore failed schedule input by form
Failed inline edits restore old input only to the submitted jadwal ID and reopen that row. Bulk input is atomic: validation/conflict failure rolls back the whole batch, flashes input, and rebuilds every draft row including blank values and ket[]. Repeated searchable selects must opt out of unscoped old input to avoid mixing scalar edit fields with bulk arrays.

## Separate schedule management from teacher attendance
Teachers may view jadwal.index but must not have attendance actions there; use manage-jadwal for those actions. Teacher attendance remains available through jadwal.presensiHarian with server-side input-presensi authorization on both form and save. These pages share attendance endpoints, so authorize the actor and schedule rather than trusting Referer or a client-supplied source flag. User.role is a UserRole enum; compare enum cases or use is-guru, never compare role directly to a string.
