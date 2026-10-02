---
paths:
  - 'app/Services/**, app/Traits/AbsensiRekapTrait.php, app/Http/Controllers/Absensi*Controller.php, app/Exports/**'
---

# Exports

## Duty checkout is presence; simple duty recaps require both sessions
piket_masuk and piket_pulang are sessions, not statuses. Successful face scans store Hadir in either session; preserve existing manual statuses on repeat scans. Pulang status means absent at home from the boarding school. Only simple/daily-summary guru piket recaps derive Alpha when exactly one attendance session exists, with a missing-entry/exit explanation; complete pairs count once per student/class/day. Detailed session reports and mapel behavior remain raw. Never persist synthetic Alpha or blindly convert manual Pulang records.
