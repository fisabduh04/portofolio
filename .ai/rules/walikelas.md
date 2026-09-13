---
paths:
  - 'resources/views/walikelas/**'
---

# Walikelas

## Wali kelas uses a single selected year and semester
The Wali Kelas page must show exactly one tahun_id (year + semester) selected in the dropdown above the table. Do not add an All Years option while the period column is hidden. Creation uses the selected period; pagination must keep it. Keep prior periods available for historical assignments.

## Wali kelas follows kelas siswa CRUD with Blade and JavaScript
Use kelassiswa/index.blade.php as the interaction reference: one shared add/edit form, bulk rows only during creation, editable employee/class/year/status/notes, modal deletion and inline status saving. The user explicitly allows correcting assignment identity during edit; do not restrict editing to status and notes. Keep the two-row responsive filter layout. The table still selects one period; the form defaults to that period but may choose a different year/semester, with successful saves returning to the saved period.

## Wali kelas feature parity includes imports exports and sorting
Wali kelas mirrors kelas siswa add/edit/delete, bulk selection, inline status, sorting, search and Excel import/export using Blade and plain JavaScript. Bulk input and imports update an existing year/class/employee assignment instead of rejecting it. Export uses selected rows or the current period and filters; exported identifiers remain text and files can be imported back. Validate the entire import before writing, allow historical inactive rows, and preserve one active wali per class and period.
