---
paths:
  - 'app/Http/Controllers/SiswaFaceController.php, app/Console/Commands/PruneInactiveFaceSamples.php, resources/js/siswa-face-enrollment.js, resources/views/siswa/face-enrollment.blade.php'
---

# Siswa

## Face reenrollment replaces stored samples instead of retaining inactive versions
Keep only the latest three student face samples. Validate first, then lock the student and delete old samples plus insert replacements in one transaction; failed replacement must retain previous samples. Explicit deletion requires admin/operator access and confirmation of the selected student, uses the same student lock, and must not delete student profiles or attendance history. Legacy cleanup targets only inactive student samples, not active samples or employee samples.
