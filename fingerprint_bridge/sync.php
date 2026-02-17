<?php

require 'vendor/autoload.php';

use Rats\Zkleib\ZKTeco;
use GuzzleHttp\Client;

// --- KONFIGURASI ---
$machine_ip = '192.168.1.201'; // IP Mesin Fingerprint di Sekolah
$machine_port = 4370;          // Port default
$api_url = 'https://YOUR-WEBSITE.com/api/attendance/push'; // Ganti dengan URL Hosting Anda
$machine_sn = 'SN-BRIDGE-PC';  // Serial Number Mesin atau Identitas Komputer Ini

// --- MULAI PROSES ---
echo "========================================\n";
echo " Fingerprint Bridge Sync Script \n";
echo "========================================\n";
echo "[INFO] Menghubungkan ke Mesin ($machine_ip)...\n";

$zk = new ZKTeco($machine_ip, $machine_port);

if ($zk->connect()) {
    echo "[OK] Terhubung ke Mesin!\n";

    // 1. Ambil Data Absensi
    echo "[INFO] Mengambil Log Absensi...\n";
    $attendance = $zk->getAttendance();
    
    if (empty($attendance)) {
        echo "[INFO] Tidak ada data absensi baru.\n";
        $zk->disconnect();
        exit;
    }

    echo "[INFO] Ditemukan " . count($attendance) . " record.\n";

    // 2. Siapkan Data untuk API
    $logsForApi = "";
    foreach ($attendance as $att) {
        /*
         ZKLib format example:
         [
           'uid' => 1,
           'id' => 1,
           'state' => 1,
           'timestamp' => '2024-01-01 08:00:00',
           'type' => 1
         ]
        */
        
        // Format String Tab-Separated sesuai parser controller kita
        // ID User <tab> Waktu <tab> Status <tab> Verify
        $logsForApi .= $att['id'] . "\t" . $att['timestamp'] . "\t" . $att['state'] . "\t" . $att['type'] . "\n";
    }

    // 3. Kirim ke Hosting API
    echo "[INFO] Mengirim data ke Hosting...\n";
    
    try {
        $client = new Client();
        $response = $client->post($api_url, [
            'form_params' => [
                'sn' => $machine_sn,
                'table' => 'attlog',
                'stamp' => date('Y-m-d H:i:s'),
            ],
            'body' => $logsForApi // Kirim Raw Body untuk data bulk
        ]);

        $body = $response->getBody();
        echo "[API RESPONSE] " . $body . "\n";

        if (strpos($body, 'POST DATA OK') !== false) {
            echo "[SUCCESS] Data berhasil disinkronkan!\n";
            
            // OPTIONAL: Hapus log di mesin agar tidak penuh (Hati-hati!)
            // $zk->clearAttendance(); 
            // echo "[INFO] Log di mesin dihapus.\n";
        } else {
            echo "[ERROR] API menolak data.\n";
        }

    } catch (\Exception $e) {
        echo "[ERROR] Gagal kirim ke API: " . $e->getMessage() . "\n";
    }

    // 4. Tutup Koneksi
    $zk->disconnect();
    echo "[INFO] Koneksi ditutup.\n";

} else {
    echo "[ERROR] Gagal terhubung ke Mesin Fingerprint ($machine_ip).\n";
    echo "Periksa kabel LAN, IP Address, dan pastikan mesin menyala.\n";
}

echo "========================================\n";
echo " Selesai. \n";
