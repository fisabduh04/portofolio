---
paths:
  - 'app/Http/Middleware/RestrictTreasurerAccess.php, app/Providers/AppServiceProvider.php, routes/web.php, resources/views/components/layout/**'
---

# Components Layout

## Treasurers can access only payroll school modules
Bendahara has payroll-only module access: deny attendance, reports, account management, and master data both in navigation and server requests. Redirect the general dashboard to payroll before querying school summaries. Master routes must retain can:manage-data-master so Livewire persistent authorization rejects snapshots after a role change. Authentication, password recovery, and logout remain available.
