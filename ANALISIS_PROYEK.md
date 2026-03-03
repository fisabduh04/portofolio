# Analisis Proyek Laravel — Sistem Informasi Sekolah

Dokumen ini berisi analisis proyek Anda sebagai pemula yang belajar otodidak: **kelebihan**, **kekurangan**, **saran**, dan **potensi pengembangan**.

---

## Ringkasan Proyek

Anda membangun **Sistem Informasi Sekolah** dengan fitur:

- **Master data:** Tahun ajaran, kelas, jurusan, mapel, pegawai, siswa, kelas-siswa, sekolah
- **Jadwal:** Jadwal mengajar, jadwal piket, jadwal wajib hadir (mandatory schedule)
- **Absensi siswa:** Presensi harian, rekap, export
- **Absensi pegawai:** Aturan kehadiran, fingerprint (ZKTeco), wajib hadir, izin, payroll, event khusus
- **Auth:** Login, 2FA, Fortify, role (kepala, admin, operator, staff, guru)

**Tech stack:** Laravel 12, PHP 8.2+, Livewire 3, Fortify, Sanctum, Maatwebsite Excel, ZKTeco.

---

## Kelebihan (Yang Sudah Bagus)

### 1. Struktur proyek rapi
- Folder standar Laravel dipakai dengan benar (`app`, `routes`, `resources`, `database`, dll).
- Ada pemisahan jelas: Controllers, Models, Livewire, View Components, Exports/Imports.
- Ada **Service** (`AttendanceService`), **Trait** (`AbsensiRekapTrait`), **Policies** (`UserPolicy`) — menunjukkan Anda sudah mencoba best practice.

### 2. Fitur lengkap dan “real-world”
- Bukan hanya CRUD sederhana: ada integrasi fingerprint, payroll, 2FA, export/import Excel.
- Role-based access (middleware `role` + `active`) diterapkan di routes.
- Transaksi database dipakai di tempat kritis (misalnya `MandatoryScheduleController::store` dengan `DB::transaction`).

### 3. Model dan relasi
- Model seperti `Pegawai` punya relasi yang masuk akal: `user`, `jadwals`, `wajibHadirs`, `fingerprintEnrollments`, dll.
- Ada scope berguna (misalnya `Tahun::aktif()`).
- Accessor untuk kompatibilitas (`getFingerprintIdAttribute` di Pegawai) menunjukkan pemahaman Eloquent.

### 4. Keamanan dasar
- Middleware `active` dan pengecekan role mencegah akses tidak sah.
- Ada 2FA untuk akun sensitif.

### 5. Testing
- Ada file test (Feature & Unit), termasuk untuk fingerprint dan wajib hadir — langkah bagus untuk pemula.

---

## Kekurangan & Area Perbaikan

### 1. Validasi di controller (belum pakai Form Request)
- Validasi pakai `$request->validate()` langsung di controller.
- **Dampak:** Kode validation berulang, controller jadi gemuk, sulit dipakai ulang.
- **Saran:** Pindahkan ke **Form Request** (`php artisan make:request NamaRequest`) untuk store/update, agar controller lebih bersih dan validasi konsisten.

### 2. Penulisan `use` model tidak konsisten (PSR-4)
- Beberapa file pakai nama kelas **huruf kecil** untuk model:
  - `use App\Models\pegawai;` (PegawaiController, PegawaiExport)
  - `use App\Models\siswa;` (SiswaImport)
  - `use App\Models\jenispilihan;` (JenispilihanController)
- Di PHP, nama kelas seharusnya **PascalCase**. Meski kadang jalan di Windows, di Linux atau environment lain bisa error.
- **Saran:** Ubah semua ke PascalCase: `Pegawai`, `Siswa`, `Jenispilihan`.

### 3. Duplikasi route
- Di `routes/web.php` baris 185 dan 187, route `POST /jadwal/import` didefinisikan **dua kali**.
- **Saran:** Hapus satu baris duplikat.

### 4. File / class legacy
- Ada `dashboard.php` dengan class `dashboard` (huruf kecil) — kemungkinan sisa lama atau typo.
- **Saran:** Pastikan tidak dipakai di route; kalau tidak, hapus atau gabung ke `DashboardController`.

### 5. Keamanan .env.example
- `.env.example` seharusnya **tanpa** rahasia (APP_KEY, MAIL_USERNAME, MAIL_PASSWORD asli).
- **Saran:** Ganti dengan placeholder (misalnya `your-app-key`, `your-email@gmail.com`, `your-password`) agar tidak bocor saat dishare atau di Git.

### 6. Controller “gemuk”
- Beberapa controller punya banyak tanggung jawab (CRUD + export + import + logic khusus).
- **Saran (jangka panjang):** Pertimbangkan **Action classes** atau **Service** untuk logic rumit agar controller hanya mengatur HTTP request/response.

### 7. Backup dan folder duplikat di repo
- Ada folder `backups/` dan `factories - Copy` di project.
- **Saran:** Jangan commit backup ke Git; tambahkan ke `.gitignore`. Folder copy sebaiknya dibersihkan atau di-ignore.

---

## Saran Praktis (Prioritas)

| Prioritas | Aksi |
|-----------|------|
| **Tinggi** | Hapus duplikat route `POST /jadwal/import` di `web.php`. |
| **Tinggi** | Perbaiki semua `use App\Models\xxx` ke PascalCase (Pegawai, Siswa, Jenispilihan, dll). |
| **Tinggi** | Bersihkan `.env.example` dari APP_KEY dan password asli; pakai placeholder. |
| **Sedang** | Untuk 1–2 controller (misalnya Pegawai atau Jadwal), buat Form Request untuk `store` dan `update`. |
| **Sedang** | Pastikan route tidak memakai `dashboard` (class lama); hapus file lama jika tidak dipakai. |
| **Rendah** | Tambah `.gitignore` untuk `backups/` dan folder copy; kurangi commit yang tidak perlu. |

---

## Apakah Anda Bisa Mengembangkan Kode Ini?

**Ya, Anda bisa.** Alasan singkat:

1. **Dasar sudah benar:** Struktur Laravel, model, relasi, middleware, dan pembagian fitur sudah masuk akal.
2. **Fitur yang Anda buat tidak trivial:** Fingerprint, payroll, 2FA, Excel, role — ini level di atas tutorial CRUD.
3. **Anda sudah pakai transaksi, scope, accessor, dan sedikit service/trait** — ini pondasi yang tepat untuk langkah berikutnya.

Yang perlu Anda tingkatkan:

- **Konsistensi:** Penamaan (PascalCase), tidak duplikat route, tidak simpan rahasia di `.env.example`.
- **Organisasi kode:** Form Request, mungkin lebih banyak Service/Action untuk logic berat.
- **Testing:** Perlahan tambah test untuk fitur kritis (absensi, payroll) agar refactor aman.
- **Dokumentasi:** README singkat (cara setup, env, role default) sangat membantu untuk diri sendiri dan orang lain.

---

## Langkah Berikut yang Bisa Anda Coba

1. Perbaiki poin “prioritas tinggi” di atas (route duplikat, use statement, .env.example).
2. Pilih satu modul (misalnya “Pegawai” atau “Jadwal”), lalu:
   - Buat Form Request untuk create/update.
   - Pindahkan logic yang berat ke satu Service class.
3. Tambah 1–2 test Feature untuk flow penting (misalnya: simpan jadwal wajib hadir, lalu cek di DB).
4. Baca dokumentasi Laravel tentang [Form Validation](https://laravel.com/docs/validation#form-request-validation) dan [Database Transactions](https://laravel.com/docs/database#database-transactions) — Anda sudah menyentuh keduanya, tinggal rapikan.

---

**Kesimpulan:** Untuk pemula otodidak, level proyek ini **sudah bagus**. Yang kurang terutama konsistensi dan sedikit pola (Form Request, pembersihan file/rahasia). Dengan perbaikan bertahap di atas, kode Anda siap untuk dikembangkan lagi (fitur baru, performa, keamanan) tanpa harus mengubah semuanya sekaligus.
