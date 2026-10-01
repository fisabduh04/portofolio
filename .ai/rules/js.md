---
paths:
  - resources/js/face-attendance.js
---

# Js

## Face attendance timings distinguish device, request and controller work
Measure device detection and full fetch/JSON round trip with a monotonic browser clock. server_ms measures only controller validation, matching and persistence, not hosting queues, bootstrap or middleware; round trip minus server_ms is not pure network latency. Clear per-scan metrics on each attempt, exclude failures from rolling success averages, and never describe local .test measurements as production Hostinger performance.

## Automatic queue scanning waits for departure and serializes requests
Automatic scanning must issue only one detection/request at a time, schedule the next cycle only after completion, and wait for two consecutive no-face observations after a successful or rejected match before submitting again. Multiple faces do not release the departure gate. Pause on transport/session/server failures; stop timers on camera shutdown or hidden pages. Keep the latest completed performance metrics while waiting for a face. Never infer capacity for 1,000 students from the HTTP rate limit alone.
