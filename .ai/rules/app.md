---
paths:
  - 'app/**/FaceAttendance*'
---

# App

## Face attendance shares manual logbooks and class locks
Teacher face attendance is limited to the authorized current-day schedule and its class/period during lesson hours. Duty scanning uses active placements across all active periods and the same class/day/piket_masuk or piket_pulang logbook identity as manual attendance. Match descriptors on the server; never return stored descriptors to the browser. Preserve existing attendance statuses on repeat scans. Both face and manual writers lock the class before finding/creating a logbook to prevent duplicates.
