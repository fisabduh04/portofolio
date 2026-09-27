---
paths:
  - app/Http/Controllers/AbsensiController.php
  - app/Http/Controllers/GuruAbsensiController.php
  - app/Http/Controllers/PegawaiWajibHadirController.php
---

# Controllers

## Daily duty attendance uses the selected date and all active periods
Daily class attendance uses GET date and validated POST tanggal for duty authorization, schedule lookup, logbook identity, display, and redirects. Allow assigned duty teachers to reach the date picker outside their duty day; authorize form/save against the selected weekday. Search schedules and duty assignments across all active academic periods, never only Tahun::aktif()->first(); load the class roster from the selected schedule's period. Keep isPiketToday for actions explicitly tied to today.

## Manual teacher attendance uses shared results and weekday obligations
The user requires /attendance/create to use pegawai_absensis (the fingerprint results table), with manual entries protected from fingerprint recalculation and no fabricated scan logs. List and accept only active Guru employees required on the selected weekday by the same active period as attendance/wajib-hadir: union of saved wajibHadirs, teaching schedules and duty schedules. Reuse Pegawai::wajibHadirPada for both form and save; off-duty teachers must not receive Alpha records from bulk manual input.

## Wajib hadir saves reject incomplete and stale forms
Wajib hadir edits must submit the displayed tahun_id, schedule version and a completeness marker placed after the checkbox list. Validate weekdays and employee IDs, lock the active year, and reject stale versions before writing. Update only the visible active employees in that period; preserve inactive employees and historical periods. Never delete the whole year's schedules merely because a request lacks manual_days.
