---
paths:
  - 'app/Enums/UserRole.php, app/Providers/AppServiceProvider.php, routes/web.php, resources/views/components/layout/sidebar.blade.php'
---

# Layout

## Payroll permission is separate from personnel administration
Only active kepala/admin/bendahara accounts may access payroll and any salary slips through manage-payroll. Operator must not see the Kepegawaian menu or access its routes, including payroll. Personnel administration remains kepala/admin only. Keep enum permissions, gates, routes, and navigation consistent; role hierarchy does not grant all specialized permissions. All permission gates deny inactive accounts.
