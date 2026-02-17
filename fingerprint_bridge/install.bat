@echo off
title Installer Bridge Fingerprint
color 1F

echo ========================================================
echo      INSTALLER OTOMATIS BRIDGE FINGERPRINT
echo              (Siap Pakai)
echo ========================================================
echo.

:: 1. Cek PHP
php -v >nul 2>&1
if %errorlevel% neq 0 (
    color 4F
    echo [ERROR] PHP tidak ditemukan di sistem Anda!
    echo.
    echo Silakan install PHP terlebih dahulu.
    echo Download: https://windows.php.net/download/
    echo.
    pause
    exit
)
echo [OK] PHP Terdeteksi.

:: 2. Cek Composer
composer -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [INFO] Composer tidak ditemukan. Mencoba mendownload composer.phar...
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
    set COMPOSER_CMD=php composer.phar
) else (
    set COMPOSER_CMD=composer
)
echo [OK] Composer Siap.

:: 3. Install Dependencies
echo.
echo [PROCESS] Sedang menginstall library pendukung...
call %COMPOSER_CMD% install --no-interaction --quiet
echo [OK] Library berhasil diinstall.

:: 4. Konfigurasi User
echo.
echo ========================================================
echo      KONFIGURASI MESIN & WEBSITE
echo ========================================================
echo.
set /p ip_mesin="Masukkan IP Mesin Fingerprint (Contoh: 192.168.1.201): "
set /p url_web="Masukkan URL Website Pesantren (Contoh: https://sekolah.com/api/attendance/push): "

echo.
echo [PROCESS] Menyimpan konfigurasi...
php config_helper.php "%ip_mesin%" "%url_web%"

:: 5. Buat Jadwal Otomatis (Task Scheduler)
echo.
echo [PROCESS] Mendaftarkan jadwal otomatis (Setiap 1 Jam)...
schtasks /create /tn "SyncFingerprintSekolah" /tr "%~dp0\run_manual.bat" /sc hourly /f >nul 2>&1

if %errorlevel% equ 0 (
    echo [OK] Jadwal berhasil dibuat! Script akan berjalan otomatis tiap jam.
) else (
    echo [WARNING] Gagal membuat jadwal otomatis. (Mungkin butuh Run as Administrator)
    echo Anda tetap bisa menjalankan sync secara manual lewat run_manual.bat
)

echo.
echo ========================================================
echo      INSTALASI SELESAI!
echo ========================================================
echo.
echo Anda bisa menutup jendela ini.
echo Script sudah siap digunakan.
pause >nul
