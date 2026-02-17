@echo off
title FINGERPRINT CONNECTION MONITOR
color 4F

:SETUP
cls
echo ========================================================
echo      SISTEM MONITOR KONEKSI MESIN FINGERPRINT
echo ========================================================
echo.
echo Pastikan kabel LAN terhubung ke Mesin Fingerprint.
echo.
set /p target_ip="Masukkan IP Mesin (Default: 192.168.1.201): "
if "%target_ip%"=="" set target_ip=192.168.1.201

:CHECK_CONNECTION
cls
echo ========================================================
echo      MEMERIKSA KONEKSI KE %target_ip% ...
echo ========================================================
echo.

ping -n 1 -w 1000 %target_ip% >nul

if %errorlevel% equ 0 (
    goto CONNECTED
) else (
    color 4F
    echo [STATUS] TERPUTUS (DISCONNECTED)
    echo.
    echo Menunggu kabel LAN dicolok...
    echo (Cek koneksi fisik dan pastikan mesin Hidup)
    
    timeout /t 2 >nul
    goto CHECK_CONNECTION
)

:CONNECTED
color 2F
cls
echo ========================================================
echo      KONEKSI BERHASIL (CONNECTED)
echo ========================================================
echo.
echo [OK] Mesin Fingerprint Terdeteksi!
echo [OK] IP: %target_ip% is Online.
echo.
echo Meluncurkan proses sinkronisasi dalam 3 detik...
timeout /t 3 >nul

:: Cek apakah sudah pernah di-install (ada vendor)
if exist "vendor" (
    :: Jika sudah install, langsung sync manual
    call run_manual.bat
) else (
    :: Jika belum, jalanin installer
    call install.bat
)

pause
