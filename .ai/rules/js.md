---
paths:
  - resources/js/face-attendance.js
---

# Js

## Face attendance timings distinguish device, request and controller work
Measure device detection and full fetch/JSON round trip with a monotonic browser clock. server_ms measures only controller validation, matching and persistence, not hosting queues, bootstrap or middleware; round trip minus server_ms is not pure network latency. Clear per-scan metrics on each attempt, exclude failures from rolling success averages, and never describe local .test measurements as production Hostinger performance.

## Automatic queue scanning waits for departure and serializes requests
Automatic scanning must issue only one detection/request at a time and schedule the next cycle only after completion. Wait for two consecutive no-face observations only after successful or already-recorded attendance; multiple faces do not release this departure gate. Retry HTTP 422 descriptor rejections with matching.status unknown or ambiguous using a fresh frame after 2 seconds, without requiring departure or relaxing matching thresholds. Other validation, transport, session and server failures pause automatic scanning. Stop timers on pause, camera shutdown or hidden pages. Keep latest completed performance metrics while waiting for a face; never infer capacity for 1,000 students from the HTTP rate limit alone.
