# Fingerprint Bridge Script

Script ini berfungsi sebagai perantara untuk menarik data dari Mesin Fingerprint (ZKTeco/Solution) dan mengirimnya ke Hosting App Absensi.

## Persyaratan
1.  **Komputer/Laptop** yang terhubung jaringan LAN dengan Mesin Fingerprint (Satu jaringan).
2.  **PHP** terinstall di komputer tersebut (Minimal versi 7.4 atau 8.x).
3.  **Composer** (Opsional, jika ingin install manual).

## Cara Instalasi

### 1. Download PHP (Jika belum ada)
- Download PHP untuk Windows: [https://windows.php.net/download/](https://windows.php.net/download/)
- Ekstrak ke `C:\php`
- Tambahkan `C:\php` ke **Environment Variables > Path**.

### 2. Siapkan Folder Ini
- Letakkan folder `fingerprint_bridge` ini di `C:\fingerprint_bridge` (atau lokasi aman lainnya).
- Buka Terminal (CMD/PowerShell) di folder tersebut.
- Jalankan perintah:
  ```bash
  composer install
  ```
- Ini akan mendownload library yang dibutuhkan.

### 3. Konfigurasi
- Buka file `sync.php` dengan Notepad/VS Code.
- Edit bagian **KONFIGURASI**:
  ```php
  $machine_ip = '192.168.1.201'; // IP Mesin Fingerprint Anda
  $api_url = 'https://website-sekolah-anda.com/api/attendance/push'; // URL Hosting
  ```

### 4. Uji Coba Manual
- Jalankan perintah:
  ```bash
  php sync.php
  ```
- Jika sukses, akan muncul pesan `[SUCCESS] Data berhasil disinkronkan!`.

## Otomatisasi (Task Scheduler)
Agar script jalan otomatis setiap jam (tanpa perlu dijalankan manual):

1.  Buka **Task Scheduler** di Windows.
2.  Klik **Create Basic Task**.
3.  Nama: `Sync Absensi Sekolah`.
4.  Trigger: **Daily** -> **Repeat task every 1 hour**.
5.  Action: **Start a Program**.
    - Program/script: `php.exe` (atau path lengkap `C:\php\php.exe`)
    - Add arguments: `sync.php`
    - Start in: `C:\fingerprint_bridge\` (Wajib diisi agar file `vendor` terbaca)
6.  Selesai.

---
**Catatan Penting:** 
Pastikan IP Mesin Fingerprint statis (tidak berubah-ubah) agar script selalu bisa terhubung.
