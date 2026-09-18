---
paths:
  - 'app/Livewire/Tahun/**'
---

# Tahun

## Protect academic year history during deletion
Only active admin/operator accounts may delete unused inactive academic years. Reject deletion if any jadwal, kelas siswa, jadwal piket, hari libur, pegawai wajib hadir, pegawai rule allocation, or wali kelas references the year, including inactive assignments. Preserve historical years by deactivation; keep tahun_id foreign keys restrictive and show usage reasons through the existing toast. Log successful deletion with actor and year identity.
