---
paths:
  - 'resources/views/kelassiswa/**'
---

# Kelassiswa

## Class assignment forms and saved status
Use siswa_id, kelas_id, tahun_id and ket consistently for the shared add/edit form; keep old-name compatibility at the controller boundary for already-open forms. Closing/reopening an add panel preserves drafts. Inline status changes require a confirmed JSON result; restore the last saved choice on failure.
