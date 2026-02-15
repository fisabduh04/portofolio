# PowerShell Safety Check Script for Laravel Portofolio
# Simpan di: scripts\safety-check.ps1

Write-Host "--- Memulai Safety Check ---" -ForegroundColor Cyan

# 1. Cek Koneksi DB & Migrasi
Write-Host "1. Mengecek database..."
php artisan migrate:status
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Terjadi masalah pada database!" -ForegroundColor Red
    exit 1
}

# 2. Jalankan Testing
Write-Host "2. Menjalankan Laravel Tests..."
php artisan test
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Beberapa test gagal! Jangan merge ke main dulu." -ForegroundColor Red
    exit 1
}

# 3. Cek Status Git
Write-Host "3. Mengecek status Git..."
$branch = git rev-parse --abbrev-ref HEAD
Write-Host "Branch aktif: $branch"

if ($branch -eq "main") {
    Write-Host "WARNING: Anda berada di branch MAIN. Sangat disarankan bekerja di branch feature/." -ForegroundColor Yellow
}

Write-Host "`n--- SEMUA OK! KODE AMAN ---" -ForegroundColor Green
