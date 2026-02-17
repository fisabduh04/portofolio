@echo off
title Manual Sync Fingerprint
color 0A

echo ==========================================
echo      MANUAL SYNC FINGERPRINT TO WEB
echo ==========================================
echo.
echo Sedang menjalankan sinkronisasi...
echo Mohon tunggu sebentar...
echo.

php sync.php

echo.
echo ==========================================
echo Selesai.
echo Tekan tombol apa saja untuk keluar...
pause >nul
