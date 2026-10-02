---
paths:
  - 'app/Enums/UserRole.php, app/Policies/UserPolicy.php, app/Http/Middleware/RoleMiddleware.php'
---

# Middleware

## School account role hierarchy
Kepala Sekolah > Administrator > Operator > Guru/Staff/Bendahara > Siswa. Only active management accounts (kepala/admin/operator) may manage accounts at or below their rank; prohibit self-management and higher-role assignment server-side. Kepala may enter role-restricted modules, but domain validation still applies. Piket/wali kelas are dated assignments, not account roles.
