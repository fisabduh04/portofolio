<?php
// Script ini dipanggil oleh install.bat untuk update config di sync.php

if ($argc < 3) {
    echo "Usage: php config_helper.php <IP_MESIN> <WEB_URL>\n";
    exit(1);
}

$ip = $argv[1];
$url = $argv[2];
$file = 'sync.php';

if (!file_exists($file)) {
    echo "Error: File sync.php tidak ditemukan.\n";
    exit(1);
}

$content = file_get_contents($file);

// Regex replace untuk konfigurasi
$content = preg_replace("/\\\$machine_ip\s*=\s*['\"].*?['\"];/", "\$machine_ip = '$ip';", $content);
$content = preg_replace("/\\\$api_url\s*=\s*['\"].*?['\"];/", "\$api_url = '$url';", $content);

file_put_contents($file, $content);

echo "Konfigurasi sync.php berhasil diupdate.\n";
echo "IP Mesin: $ip\n";
echo "Web URL : $url\n";
