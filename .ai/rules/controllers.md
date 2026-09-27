---
paths:
  - app/Http/Controllers/AbsensiController.php
---

# Controllers

## Daily duty attendance uses the selected date and all active periods
Daily class attendance uses GET date and validated POST tanggal for duty authorization, schedule lookup, logbook identity, display, and redirects. Allow assigned duty teachers to reach the date picker outside their duty day; authorize form/save against the selected weekday. Search schedules and duty assignments across all active academic periods, never only Tahun::aktif()->first(); load the class roster from the selected schedule's period. Keep isPiketToday for actions explicitly tied to today.
