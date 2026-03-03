# Panduan Deploy ke Hostinger

Ikuti langkah berikut **berurutan**. Centang ✓ saat selesai.

---

## Checklist singkat

- [ ] **Lokal:** Build asset (`npm run build`)
- [ ] **Lokal:** Pastikan `.env` tidak ikut (sudah di .gitignore)
- [ ] **Upload** proyek ke Hostinger (Git atau File Manager)
- [ ] **Server:** Buat file `.env` dari `.env.example`, isi nilai production
- [ ] **Server:** `composer install --no-dev`, `php artisan migrate`, cache, storage:link
- [ ] **Hostinger panel:** Document root = folder `public`
- [ ] **Test:** Buka URL domain, cek login & fitur penting

---

## 1. Persiapan di komputer Anda (sebelum upload)

### 1.1 Build CSS & JS (wajib)

Proyek pakai Vite. Sebelum upload, build dulu agar file CSS/JS siap dipakai di server:

```bash
npm install
npm run build
```

Ini membuat folder `public/build` berisi file yang dipakai production. **Upload folder `public/build`** ke Hostinger (atau upload seluruh proyek setelah build).

### 1.2 Jangan upload

- Folder `node_modules` (tidak perlu di server)
- File `.env` (rahasia; buat baru di server dari `.env.example`)

---

## 2. Upload proyek ke Hostinger

- **Opsi A (disarankan):** Push ke GitHub/GitLab, lalu di Hostinger pakai **Git** (Deploy from repository / Clone) jika tersedia.
- **Opsi B:** Upload lewat **File Manager** atau **FTP**:
  - Upload semua file/folder **kecuali** `node_modules`, `.env`, `vendor` (nanti di-install di server).
  - Atau upload semuanya termasuk `vendor` dan `public/build` jika sudah di-build di lokal — lebih cepat di server.

---

## 3. File `.env` di server Hostinger

**Penting:** File `.env` **tidak** ikut di Git (aman). Di server Hostinger Anda buat `.env` sendiri.

### Mana yang enak: SSH atau ketik manual?

| Cara | Enak? | Keterangan |
|------|--------|------------|
| **SSH** | ✅ Paling enak | Copy `.env.example` → `.env` di server, lalu **paste** isi yang sudah Anda siapkan di komputer. Tidak perlu ketik satu per satu. |
| **File Manager** | ✅ Juga enak | Copy file `.env.example` jadi `.env`, buka **Edit**, lalu **paste** isi yang sama. Juga tidak perlu ketik manual. |

**Kesimpulan:** Anda **tidak perlu ketik manual** semua baris. Siapkan isi `.env` production **di komputer** (lihat bawah), lalu di Hostinger **paste** saja — baik lewat SSH (nano) atau lewat Edit di File Manager.

### Langkah isi `.env` (tanpa ketik manual)

1. **Di komputer Anda:** Buat/buka file sementara (misalnya `env-production.txt`). Isi dengan nilai production (APP_ENV=production, APP_DEBUG=false, APP_URL=https://domain-anda.com, DB_* dari panel Hostinger, MAIL_* dari Gmail Anda, APP_KEY bisa kosong dulu). **Jangan** simpan file ini di folder proyek yang di-commit ke Git (atau hapus setelah dipaste).
2. **Di Hostinger (pilih salah satu):**
   - **Pakai SSH:** Masuk ke folder proyek → `cp .env.example .env` → `nano .env` → hapus isi lama, **paste** isi dari `env-production.txt` → Simpan (Ctrl+O, Enter, Ctrl+X). Lalu jalankan `php artisan key:generate` agar APP_KEY terisi.
   - **Pakai File Manager:** Duplikat `.env.example` → rename jadi `.env` → Klik **Edit** → ganti isi dengan **paste** dari `env-production.txt` → Save. Untuk APP_KEY: lewat SSH jalankan `php artisan key:generate`, atau copy APP_KEY dari `.env` lokal Anda (boleh sama untuk development/production jika ini proyek pribadi; idealnya generate baru di server).
3. Selesai. Tidak perlu ketik manual.

---

**Detail variabel yang harus diisi (untuk isi `env-production.txt`):**

   | Variabel | Isi di Hostinger |
   |----------|------------------|
   | `APP_KEY` | Jalankan `php artisan key:generate` di server, atau isi key dari project lokal Anda |
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_URL` | URL domain Anda, misal `https://domainanda.com` |
   | `DB_HOST` | Host database Hostinger (biasanya `localhost` atau alamat yang diberikan Hostinger) |
   | `DB_DATABASE` | Nama database yang Anda buat di panel Hostinger |
   | `DB_USERNAME` | Username database Hostinger |
   | `DB_PASSWORD` | Password database Hostinger |
   | `MAIL_USERNAME` | Email untuk kirim mail (misal Gmail) |
   | `MAIL_PASSWORD` | Password app / app password email tersebut |
   | `MAIL_FROM_ADDRESS` | Sama dengan MAIL_USERNAME |
   | `MAIL_FROM_NAME` | Nama pengirim, misal "Sistem Informasi SMK" |

3. Generate APP_KEY di server (jika belum):
   ```bash
   php artisan key:generate
   ```

---

## 4. Jalankan perintah di server (setelah upload)

Jalankan di root proyek (SSH atau Terminal Hostinger):

```bash
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika pakai storage link untuk upload/foto:

```bash
php artisan storage:link
```

Atur permission folder `storage` dan `bootstrap/cache` agar bisa ditulis (biasanya `755` atau `775`).

**Cara jalankan:**

- **Jika punya SSH:** Buka Terminal Hostinger, masuk ke folder proyek, jalankan perintah di atas.
- **Jika tidak punya SSH (hanya File Manager):**
  1. Di **komputer Anda:** jalankan `composer install --optimize-autoloader --no-dev` dan `npm run build`.
  2. Upload **seluruh proyek** ke Hostinger (termasuk folder `vendor` dan `public/build`).
  3. Di server, buat file `.env` (copy dari `.env.example` lewat File Manager), lalu edit dan isi APP_KEY, APP_ENV=production, APP_DEBUG=false, APP_URL, DB_*, MAIL_*.
  4. Untuk **migrasi database:** export struktur+tabel dari database lokal Anda lewat phpMyAdmin, lalu import di phpMyAdmin Hostinger. Atau gunakan fitur "Run PHP script" / satu kali akses SSH jika tersedia untuk menjalankan `php artisan migrate --force`.

---

## 5. Document root

Arahkan **document root** ke folder **`public`** (bukan ke root proyek).

- **Hostinger:** **hPanel → Websites → domain Anda → Advanced → Document Root**
- Pilih path yang mengarah ke **`.../public`** (misalnya `domains/namadomain.com/public_html/public` jika proyek ada di `public_html`).

Tanpa ini, Laravel tidak jalan dan bisa terbuka file sensitif.

---

## Ringkasan

| Di Git / .env.example | Di server (file .env) |
|------------------------|------------------------|
| Hanya **placeholder** (kosong atau contoh) | Isi **nilai asli** (APP_KEY, DB_*, MAIL_*, APP_URL) |
| Aman diupload / di-push | **Jangan** commit `.env` ke Git |

Dengan begitu `.env.example` tetap dipakai sebagai **template** saat deploy, tanpa menyimpan password/APP_KEY di Git.
