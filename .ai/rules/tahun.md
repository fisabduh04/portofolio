---
paths:
  - 'app/Livewire/Tahun/**'
---

# Tahun

## Protect academic year history during deletion
Active kepala/admin/operator accounts may delete unused inactive academic years. Reject deletion if any jadwal, kelas siswa, jadwal piket, hari libur, pegawai wajib hadir, pegawai rule allocation, or wali kelas references the year, including inactive assignments. Preserve historical years by deactivation; retain restrictive foreign keys and usage reasons. Log successful deletion with actor and year identity.
