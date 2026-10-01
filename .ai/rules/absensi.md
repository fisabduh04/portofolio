---
paths:
  - 'resources/js/attendance-live-recap.js, resources/views/absensi/rekap*.blade.php'
---

# Absensi

## Attendance recap updates preserve filters and reuse authorized report routes
Student recap pages poll their existing authenticated GET URL every 5 seconds after a completed request; this is near-real-time polling, not WebSocket push. Replace only data-live-recap-content, never filters or the layout. Keep one request in flight, suspend when hidden/offline, back off on errors, and retain old data with a visible last-sync status. Charts must be destroyed before region replacement and rebuilt afterward. Do not describe polling interval as measured end-to-end latency.
