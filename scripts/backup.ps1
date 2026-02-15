# PowerShell Backup Script for Laravel Portofolio
# Simpan di: scripts\backup.ps1

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupDir = "backups\backup_$timestamp"

# Buat folder backup
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

Write-Host "--- Memulai Backup ($timestamp) ---" -ForegroundColor Cyan

# Daftar folder/file yang wajib dibackup
$targets = @("app", "resources", "routes", "database", "config", ".env")

foreach ($target in $targets) {
    if (Test-Path $target) {
        Write-Host "Backing up: $target..."
        Copy-Item -Path $target -Destination "$backupDir\$target" -Recurse -Force
    }
}

Write-Host "`nBackup berhasil disimpan di: $backupDir" -ForegroundColor Green
Write-Host "----------------------------------"
